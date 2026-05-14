<?php

namespace App\Http\Controllers\Customer;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Issue;
use App\Models\Review;
use App\Models\Service;
use App\Notifications\BookingPlacedNotification;
use App\Notifications\BookingRescheduledNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(): View
    {
        $bookings = Booking::query()
            ->where('customer_id', auth()->id())
            ->with(['assignedStaff', 'items.service'])
            ->latest()
            ->paginate(15);

        return view('customer.bookings.index', compact('bookings'));
    }

    public function reviewsIndex(): View
    {
        $reviews = Review::query()
            ->where('customer_id', auth()->id())
            ->with('booking')
            ->latest()
            ->paginate(15);

        return view('customer.reviews.index', compact('reviews'));
    }

    public function issuesIndex(): View
    {
        $issues = Issue::query()
            ->where('customer_id', auth()->id())
            ->with('booking')
            ->latest()
            ->paginate(15);

        return view('customer.issues.index', compact('issues'));
    }

    public const PAYMENT_METHODS = [
        'cod' => 'Cash on Delivery',
        'gcash' => 'GCash',
        'maya' => 'Maya',
        'bank_transfer' => 'Bank Transfer',
    ];

    public function create(): View
    {
        $services = Service::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('customer.bookings.create', compact('services'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->normalizeBookingFormRequest($request);
        $validated = $this->validateBookingPayload($request);

        $booking = DB::transaction(function () use ($validated) {
            $total = 0;
            $lines = [];

            foreach ($validated['items'] as $row) {
                $service = Service::query()->whereKey($row['service_id'])->where('is_active', true)->firstOrFail();
                $qty = (int) $row['quantity'];
                $line = round((float) $service->price * $qty, 2);
                $total += $line;
                $lines[] = [
                    'service_id' => $service->id,
                    'quantity' => $qty,
                    'unit_price' => $service->price,
                    'line_total' => $line,
                ];
            }

            /** @var \App\Models\Booking $booking */
            $booking = Booking::query()->create([
                'customer_id' => auth()->id(),
                'status' => BookingStatus::Pending,
                'pickup_address' => $validated['pickup_address'],
                'delivery_address' => $validated['delivery_address'],
                'pickup_scheduled_at' => $validated['pickup_scheduled_at'],
                'delivery_scheduled_at' => $validated['delivery_scheduled_at'],
                'total_amount' => round($total, 2),
                'payment_channel' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $booking->items()->create($line);
            }

            return $booking;
        });

        $booking->load('customer');
        $booking->customer?->notify(new BookingPlacedNotification($booking));

        if ($booking->customer?->phone) {
            app(\App\Services\SmsService::class)->send(
                $booking->customer->phone,
                'CleanSwift: booking #'.$booking->id.' confirmed. Total '.$booking->total_amount.'.'
            );
        }

        return redirect()->route('customer.bookings.show', $booking)->with('status', __('Booking placed.'));
    }

    public function show(Booking $booking): View
    {
        abort_unless($booking->customer_id === auth()->id(), 403);

        $booking->load(['assignedStaff', 'items.service', 'review', 'issues']);

        return view('customer.bookings.show', compact('booking'));
    }

    public function updatePaymentMethod(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === auth()->id(), 403);

        if ($booking->isPaid()) {
            return back()->withErrors(['payment_method' => __('Cannot change payment method on a paid booking.')]);
        }

        if ($booking->isCancelled()) {
            return back()->withErrors(['payment_method' => __('Cannot change payment method on a cancelled booking.')]);
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', \Illuminate\Validation\Rule::in(array_keys(self::PAYMENT_METHODS))],
        ]);

        $booking->update(['payment_channel' => $validated['payment_method']]);

        return back()->with('status', __('Payment method updated.'));
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === auth()->id(), 403);

        if (in_array($booking->status, [BookingStatus::Delivered, BookingStatus::Cancelled], true)) {
            return back()->withErrors(['status' => __('This booking cannot be cancelled.')]);
        }

        $booking->update([
            'status' => BookingStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

        return back()->with('status', __('Booking cancelled.'));
    }

    public function reschedule(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === auth()->id(), 403);

        if (in_array($booking->status, [BookingStatus::Delivered, BookingStatus::Cancelled], true)) {
            return back()->withErrors(['status' => __('This booking cannot be rescheduled.')]);
        }

        $data = $request->validate([
            'pickup_scheduled_at' => ['required', 'date', 'after:now'],
            'delivery_scheduled_at' => ['required', 'date', 'after:pickup_scheduled_at'],
        ]);

        $booking->update([
            ...$data,
            'reschedule_count' => $booking->reschedule_count + 1,
        ]);

        if ($booking->assignedStaff) {
            $booking->loadMissing('customer');
            $booking->assignedStaff->notify(new BookingRescheduledNotification($booking));
        }

        return back()->with('status', __('Pickup and delivery times updated.'));
    }

    public function storeReview(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === auth()->id(), 403);
        abort_unless($booking->status === BookingStatus::Delivered, 403);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $booking->review()->updateOrCreate(
            [],
            [
                'customer_id' => auth()->id(),
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]
        );

        return back()->with('status', __('Thank you for your feedback.'));
    }

    public function storeIssue(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->customer_id === auth()->id(), 403);
        abort_unless($booking->status === BookingStatus::Delivered, 403);

        $data = $request->validate([
            'description' => ['required', 'string', 'max:5000'],
        ]);

        $booking->issues()->create([
            'customer_id' => auth()->id(),
            'description' => $data['description'],
        ]);

        return back()->with('status', __('Your issue has been reported. We will look into it.'));
    }

    private function normalizeBookingFormRequest(Request $request): void
    {
        foreach (['pickup_address', 'delivery_address', 'notes'] as $field) {
            $value = $request->input($field);
            if (is_array($value)) {
                $request->merge([
                    $field => implode("\n", array_map(static fn ($v) => is_scalar($v) ? (string) $v : '', $value)),
                ]);
            }
        }

        $rawItems = $request->input('items', []);
        $normalized = [];

        if (is_array($rawItems) && $rawItems !== []) {
            $first = reset($rawItems);
            $isNestedRows = is_array($first) && (array_key_exists('service_id', $first) || array_key_exists('quantity', $first));

            if ($isNestedRows) {
                foreach ($rawItems as $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $sid = (int) ($row['service_id'] ?? 0);
                    $qty = max(0, (int) ($row['quantity'] ?? 0));
                    if ($sid > 0 && $qty > 0) {
                        $normalized[] = ['service_id' => $sid, 'quantity' => $qty];
                    }
                }
            } else {
                $normalized = collect($rawItems)
                    ->map(fn ($qty, $serviceId) => [
                        'service_id' => (int) $serviceId,
                        'quantity' => max(0, (int) $qty),
                    ])
                    ->filter(fn (array $row) => $row['quantity'] > 0)
                    ->values()
                    ->all();
            }
        }

        $request->merge(['items' => $normalized]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateBookingPayload(Request $request): array
    {
        return $request->validate($this->bookingPayloadRules());
    }

    /**
     * @return array<string, mixed>
     */
    private function bookingPayloadRules(): array
    {
        return [
            'pickup_address' => ['required', 'string', 'max:2000'],
            'delivery_address' => ['required', 'string', 'max:2000'],
            'pickup_scheduled_at' => ['required', 'date', 'after:now'],
            'delivery_scheduled_at' => ['required', 'date', 'after:pickup_scheduled_at'],
            'payment_method' => ['required', 'string', \Illuminate\Validation\Rule::in(array_keys(self::PAYMENT_METHODS))],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => ['required', 'exists:services,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:500'],
        ];
    }
}

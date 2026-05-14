<?php

namespace App\Http\Controllers\Staff;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = $this->buildQuery($request)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('staff.bookings.index', compact('bookings'));
    }

    public function history(Request $request): View
    {
        $bookings = $this->buildQuery($request)
            ->whereIn('status', [
                BookingStatus::Delivered,
                BookingStatus::Cancelled,
            ])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('staff.bookings.history', compact('bookings'));
    }

    private function buildQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = Booking::query()
            ->where('assigned_staff_id', auth()->id())
            ->with('customer');

        if ($search = $request->string('search')->toString()) {
            $query->whereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($dateFrom = $request->string('date_from')->toString()) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->string('date_to')->toString()) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $sort = $request->string('sort')->toString();
        if ($sort === 'oldest') {
            $query->oldest();
        }

        return $query;
    }

    public function show(Booking $booking): View
    {
        abort_unless($booking->assigned_staff_id === auth()->id(), 403);

        $booking->load(['customer', 'items.service', 'review', 'issues']);

        return view('staff.bookings.show', compact('booking'));
    }

    public function advance(Booking $booking): RedirectResponse
    {
        abort_unless($booking->assigned_staff_id === auth()->id(), 403);

        if ($booking->status === BookingStatus::Cancelled || $booking->status === BookingStatus::Delivered) {
            return back()->withErrors(['status' => __('This booking cannot be updated.')]);
        }

        $steps = BookingStatus::staffProgression();
        $idx = array_search($booking->status, $steps, true);

        if ($idx === false || $idx >= count($steps) - 1) {
            return back()->withErrors(['status' => __('No further status to apply.')]);
        }

        $booking->update(['status' => $steps[$idx + 1]]);

        return back()->with('status', __('Status updated.'));
    }
}

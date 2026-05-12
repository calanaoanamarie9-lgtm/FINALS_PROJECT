<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->with(['customer', 'assignedStaff'])
            ->when($request->string('status')->toString(), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $booking->load(['customer', 'assignedStaff', 'items.service', 'review']);

        $staffMembers = User::query()
            ->where('role', \App\Enums\UserRole::Staff)
            ->where('staff_application_status', \App\Enums\StaffApplicationStatus::Approved)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.bookings.show', compact('booking', 'staffMembers'));
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_staff_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\BookingStatus::class)],
        ]);

        $booking->update($validated);

        return back()->with('status', __('Booking updated.'));
    }

    public function markPaymentPaid(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'payment_channel' => ['nullable', 'string', 'max:64'],
        ]);

        $booking->markAsPaid($validated['payment_channel'] ?? $booking->payment_channel ?? 'manual');

        return back()->with('status', __('Payment marked as paid.'));
    }

    public function markPaymentUnpaid(Booking $booking): RedirectResponse
    {
        $booking->markAsUnpaid();

        return back()->with('status', __('Payment marked as unpaid.'));
    }

    public function destroyAll(): RedirectResponse
    {
        $ids = Booking::query()->pluck('id');

        DB::transaction(function () use ($ids) {
            Transaction::query()->whereIn('booking_id', $ids)->delete();
            Booking::query()->whereIn('id', $ids)->delete();
        });

        return redirect()->route('admin.bookings.index')
            ->with('status', __('All bookings have been deleted.'));
    }
}

<?php

namespace App\Http\Controllers\Staff;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(): View
    {
        $bookings = Booking::query()
            ->where('assigned_staff_id', auth()->id())
            ->with('customer')
            ->latest()
            ->paginate(15);

        return view('staff.bookings.index', compact('bookings'));
    }

    public function history(): View
    {
        $bookings = Booking::query()
            ->where('assigned_staff_id', auth()->id())
            ->whereIn('status', [
                BookingStatus::Delivered,
                BookingStatus::Cancelled,
            ])
            ->with('customer')
            ->latest()
            ->paginate(15);

        return view('staff.bookings.history', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        abort_unless($booking->assigned_staff_id === auth()->id(), 403);

        $booking->load(['customer', 'items.service', 'review']);

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

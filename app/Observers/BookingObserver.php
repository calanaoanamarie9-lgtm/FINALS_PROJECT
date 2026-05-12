<?php

namespace App\Observers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Transaction;
use App\Notifications\BookingStatusChangedNotification;

class BookingObserver
{
    public function updated(Booking $booking): void
    {
        if (! $booking->wasChanged('status')) {
            return;
        }

        $booking->loadMissing('customer', 'assignedStaff');

        if ($booking->customer) {
            $booking->customer->notify(new BookingStatusChangedNotification($booking));
        }

        if ($booking->assignedStaff) {
            $booking->assignedStaff->notify(new BookingStatusChangedNotification($booking));
        }

        if ($booking->customer?->phone) {
            app(\App\Services\SmsService::class)->send(
                $booking->customer->phone,
                'CleanSwift: booking #'.$booking->id.' is now '.$booking->status->label().'.'
            );
        }

        if ($booking->status === BookingStatus::Delivered) {
            $exists = Transaction::query()
                ->where('booking_id', $booking->id)
                ->where('type', 'payment')
                ->exists();

            if (! $exists && (float) $booking->total_amount > 0) {
                Transaction::query()->create([
                    'booking_id' => $booking->id,
                    'amount' => $booking->total_amount,
                    'type' => 'payment',
                    'notes' => 'Recorded on delivery',
                    'recorded_at' => now(),
                ]);
            }
        }
    }
}

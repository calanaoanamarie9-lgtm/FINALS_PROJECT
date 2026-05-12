<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Support\NotificationText;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingPlacedNotification extends Notification
{
    public function __construct(public Booking $booking)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(NotificationText::asString(__('Booking confirmation #:id', ['id' => $this->booking->id])))
            ->line(NotificationText::asString(__('Thanks! We received your laundry booking.')))
            ->line(NotificationText::asString(__('Total: :amount', ['amount' => number_format((float) $this->booking->total_amount, 2)])))
            ->action(NotificationText::asString(__('Track booking')), route('customer.bookings.show', $this->booking));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => NotificationText::asString(__('Booking confirmed')),
            'body' => NotificationText::asString(__('Booking #:id was created.', ['id' => $this->booking->id])),
            'booking_id' => $this->booking->id,
        ];
    }
}

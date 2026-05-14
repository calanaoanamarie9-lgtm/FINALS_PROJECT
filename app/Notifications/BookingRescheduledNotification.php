<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Support\NotificationText;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingRescheduledNotification extends Notification
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
            ->subject(NotificationText::asString(__('Booking #:id rescheduled', ['id' => $this->booking->id])))
            ->line(NotificationText::asString(__('The schedule for booking #:id has been updated.', ['id' => $this->booking->id])))
            ->line(NotificationText::asString(__('New pickup: :time', ['time' => $this->booking->pickup_scheduled_at->timezone(config('app.timezone'))->toDayDateTimeString()])))
            ->line(NotificationText::asString(__('New delivery: :time', ['time' => $this->booking->delivery_scheduled_at->timezone(config('app.timezone'))->toDayDateTimeString()])))
            ->action(NotificationText::asString(__('View booking')), route('staff.bookings.show', $this->booking));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => NotificationText::asString(__('Booking rescheduled')),
            'body' => NotificationText::asString(__('Booking #:id schedule has changed.', ['id' => $this->booking->id])),
            'booking_id' => $this->booking->id,
        ];
    }
}

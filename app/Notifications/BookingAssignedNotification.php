<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Support\NotificationText;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingAssignedNotification extends Notification
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
            ->subject(NotificationText::asString(__('New booking assigned #:id', ['id' => $this->booking->id])))
            ->line(NotificationText::asString(__('A new booking has been assigned to you.')))
            ->line(NotificationText::asString(__('Customer: :name', ['name' => NotificationText::asString($this->booking->customer?->name ?? '—')])))
            ->line(NotificationText::asString(__('Pickup: :time', ['time' => $this->booking->pickup_scheduled_at->timezone(config('app.timezone'))->toDayDateTimeString()])))
            ->action(NotificationText::asString(__('View booking')), route('staff.bookings.show', $this->booking));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => NotificationText::asString(__('New booking assigned')),
            'body' => NotificationText::asString(__('Booking #:id assigned to you.', ['id' => $this->booking->id])),
            'booking_id' => $this->booking->id,
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\User;
use App\Support\NotificationText;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusChangedNotification extends Notification
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
        $status = NotificationText::asString($this->booking->status->label());

        $mail = (new MailMessage)
            ->subject(NotificationText::asString(__('Booking #:id — :status', ['id' => $this->booking->id, 'status' => $status])))
            ->line(NotificationText::asString(__('Current status: :status', ['status' => $status])));

        if ($notifiable instanceof User && $notifiable->id === $this->booking->customer_id) {
            $mail->line(NotificationText::asString(__('Pickup scheduled: :time', ['time' => $this->booking->pickup_scheduled_at->timezone(config('app.timezone'))->toDayDateTimeString()])));
        } else {
            $mail->line(NotificationText::asString(__('Customer: :name', ['name' => NotificationText::asString($this->booking->customer?->name ?? '—')])));
        }

        $url = route('customer.bookings.show', $this->booking);
        if ($notifiable instanceof User) {
            if ($notifiable->isAdmin()) {
                $url = route('admin.bookings.show', $this->booking);
            } elseif ($notifiable->isStaff()) {
                $url = route('staff.bookings.show', $this->booking);
            }
        }

        return $mail->action(NotificationText::asString(__('View booking')), $url);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => NotificationText::asString(__('Booking #:id updated', ['id' => $this->booking->id])),
            'body' => NotificationText::asString(__('Status is now :status', ['status' => $this->booking->status->label()])),
            'booking_id' => $this->booking->id,
        ];
    }
}

<?php

namespace App\Notifications;

use App\Support\NotificationText;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StaffApplicationReceivedNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(NotificationText::asString(__('CleanSwift staff application received')))
            ->greeting(NotificationText::asString(__('Hi :name,', ['name' => NotificationText::asString($notifiable->name)])))
            ->line(NotificationText::asString(__('Thanks for applying as laundry staff. An administrator will review your profile shortly.')))
            ->line(NotificationText::asString(__('You will be able to sign in after your application is approved.')));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => NotificationText::asString(__('Staff application received')),
            'body' => NotificationText::asString(__('We will notify you when an admin approves your account.')),
        ];
    }
}

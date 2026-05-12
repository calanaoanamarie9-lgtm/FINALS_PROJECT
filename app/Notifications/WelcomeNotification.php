<?php

namespace App\Notifications;

use App\Support\NotificationText;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(NotificationText::asString(__('Welcome to CleanSwift')))
            ->greeting(NotificationText::asString(__('Hello :name,', ['name' => NotificationText::asString($notifiable->name)])))
            ->line(NotificationText::asString(__('Your account is ready. Book laundry pickup and delivery anytime from your dashboard.')))
            ->action(NotificationText::asString(__('Go to dashboard')), url('/dashboard'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => NotificationText::asString(__('Welcome to CleanSwift')),
            'body' => NotificationText::asString(__('Registration completed successfully.')),
        ];
    }
}

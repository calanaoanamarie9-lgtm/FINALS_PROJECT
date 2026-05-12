<?php

namespace App\Notifications;

use App\Support\NotificationText;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginAlertNotification extends Notification
{
    public function __construct(public string $ipAddress)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(NotificationText::asString(__('New sign-in to your CleanSwift account')))
            ->line(NotificationText::asString(__('We noticed a successful login to your account.')))
            ->line(NotificationText::asString(__('IP address: :ip', ['ip' => $this->ipAddress])))
            ->line(NotificationText::asString(__('If this was not you, change your password immediately.')));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => NotificationText::asString(__('Account sign-in')),
            'body' => NotificationText::asString(__('Successful login from IP :ip', ['ip' => $this->ipAddress])),
        ];
    }
}

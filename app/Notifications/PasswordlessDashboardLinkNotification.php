<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordlessDashboardLinkNotification extends Notification
{
    public function __construct(private string $dashboardUrl, private bool $isNewAccount)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Access your Housing Alerts dashboard')
            ->greeting('Hi there,');

        $message->line('Thanks for signing up to help support housing in your community!');

        $message->line('Open your dashboard below to sign up for notifications when new housing events are posted.');

        return $message
            ->action('Open your dashboard', $this->dashboardUrl)
            ->line('Tip: bookmark this link so you can return anytime without requesting another email.')
            ->line('If you did not request this email, you can safely ignore it.');
    }

    public function isForNewAccount(): bool
    {
        return $this->isNewAccount;
    }
}

<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExistingPasswordlessUserNotification extends Notification
{
    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $dashboardUrl = $notifiable->getDashboardUrl();

        return (new MailMessage)
            ->subject('Your Housing Alerts dashboard link')
            ->greeting('Hi ' . ($notifiable->name ?: 'there') . '!')
            ->line('You already have a Housing Alerts account with this email address.')
            ->line('Use the link below to open your personal dashboard and manage your housing hearing subscriptions.')
            ->action('Open your dashboard', $dashboardUrl)
            ->line('Tip: bookmark this link so you can return anytime without requesting another email.')
            ->line('If you did not request this email, you can safely ignore it.');
    }
}

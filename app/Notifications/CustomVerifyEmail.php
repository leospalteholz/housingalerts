<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmailBase
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Welcome to Housing Alerts - Please Verify Your Email')
            ->greeting('Welcome to Housing Alerts!')
            ->line('Thank you for signing up to receive housing alerts in your area.')
            ->line('You\'re helping to support housing in your community by staying informed about upcoming hearings and opportunities to provide input.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('Once verified, you\'ll start receiving timely notifications about housing hearings in the regions you selected.')
            ->line('If you didn\'t create an account with Housing Alerts, please ignore this email.')
            ->salutation('Thank you for supporting housing in your community!');
    }

    /**
     * Get the verification URL for the given notifiable.
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}

<?php

namespace App\Mail;

use App\Models\Hearing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HearingNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public Hearing $hearing,
        public string $template,
        public array $templateData = []
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getSubject(),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: "emails.hearing-{$this->template}",
            with: [
                'user' => $this->user,
                'hearing' => $this->hearing,
                'data' => $this->templateData,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Generate ICS file content
        $icsContent = $this->hearing->generateIcsContent();
        $filename = 'hearing-'.$this->hearing->id.'.ics';

        return [
            Attachment::fromData(fn () => $icsContent, $filename)
                ->withMime('text/calendar'),
        ];
    }

    /**
     * Get the subject line based on template type.
     */
    private function getSubject(): string
    {
        return match ($this->template) {
            'created' => "New Hearing: {$this->hearing->title}",
            'day_of_reminder' => "Reminder: Hearing Today - {$this->hearing->title}",
            'week_reminder' => "Reminder: Hearing Next Week - {$this->hearing->title}",
            default => "Hearing Notification: {$this->hearing->title}"
        };
    }
}

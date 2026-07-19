<?php

namespace App\Mail;

use App\Models\Organizer;
use App\Traits\HasDynamicSmtp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventBroadcastMail extends Mailable
{
    use Queueable, SerializesModels, HasDynamicSmtp;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $emailSubject,
        public string $emailContent,
        public ?Organizer $organizer = null
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Apply SMTP settings
        $this->applyOrganizerSmtp($this->organizer);

        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.broadcast',
        );
    }
}

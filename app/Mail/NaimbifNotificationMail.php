<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NaimbifNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $type;
    public string $title;
    public string $activityMessage;
    public ?Application $application;
    public ?string $actionUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $type,
        string $title,
        string $activityMessage,
        ?Application $application = null,
        ?string $actionUrl = null
    ) {
        $this->type = $type;
        $this->title = $title;
        $this->activityMessage = $activityMessage;
        $this->application = $application;
        $this->actionUrl = $actionUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjectPrefix = '[NAIMbif JPVNK] ';
        if ($this->application) {
            $subject = $subjectPrefix . $this->title . ' (' . $this->application->no_rujukan . ')';
        } else {
            $subject = $subjectPrefix . $this->title;
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

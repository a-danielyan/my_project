<?php

namespace App\Mail;

use App\Models\Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public string $content;
    public string $emailSubject;
    public string $emailFrom;
    /**
     * Create a new message instance.
     */
    public function __construct(
        private Reminder $reminder,
    ) {
        $this->content = $this->reminder->reminder_text;
        $this->emailSubject = $this->reminder->subject;
        $this->emailFrom = $this->reminder->sender[0]; //@todo check what will be in sender
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->emailFrom),
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'email.raw',
            text: 'email.text_raw'
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

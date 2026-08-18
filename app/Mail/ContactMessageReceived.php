<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Notifies the site owner about a new contact-form message (sent synchronously — no queue worker on prod). Reply goes straight to the sender. */
class ContactMessageReceived extends Mailable
{
    use SerializesModels;

    public function __construct(public ContactMessage $contactMessage) {}

    public function envelope(): Envelope
    {
        $subject = $this->contactMessage->subject
            ? 'Contact form: '.$this->contactMessage->subject
            : 'New contact form message from '.$this->contactMessage->name;

        return new Envelope(
            subject: $subject,
            replyTo: [new Address($this->contactMessage->email, $this->contactMessage->name)],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.contact-message', with: ['msg' => $this->contactMessage]);
    }
}

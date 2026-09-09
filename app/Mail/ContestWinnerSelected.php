<?php

namespace App\Mail;

use App\Models\Contest;
use App\Models\ContestEntry;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Tells the winner they won (sent synchronously — no queue worker on prod). */
class ContestWinnerSelected extends Mailable
{
    use SerializesModels;

    public function __construct(public Contest $contest, public ContestEntry $entry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('You won! :contest', ['contest' => $this->contest->t('title')]),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.contest-winner', with: [
            'contest' => $this->contest,
            'entry' => $this->entry,
        ]);
    }
}

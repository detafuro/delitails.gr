<?php

namespace App\Mail;

use App\Models\Contest;
use App\Models\ContestDraw;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Tells the site owner a draw happened, with the full result. */
class ContestDrawCompleted extends Mailable
{
    use SerializesModels;

    public function __construct(public Contest $contest, public ContestDraw $draw) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Contest drawn: '.$this->contest->title);
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.contest-draw', with: [
            'contest' => $this->contest,
            'draw' => $this->draw,
        ]);
    }
}

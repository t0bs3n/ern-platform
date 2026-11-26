<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** A notice to the old address after a change (R1-12). It never names the new address. */
class EmailChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $member) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your e-mail address for ERN was changed');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.email-changed', text: 'mail.email-changed-text');
    }
}

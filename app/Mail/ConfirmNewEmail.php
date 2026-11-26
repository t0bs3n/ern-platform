<?php

namespace App\Mail;

use App\Members\EmailChangeLink;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** The link that confirms a new address, to the new address only (R1-12). */
class ConfirmNewEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $url;

    public function __construct(public User $member)
    {
        $this->url = EmailChangeLink::for($member);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Confirm your new e-mail address for ERN');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.confirm-new-email', text: 'mail.confirm-new-email-text');
    }
}

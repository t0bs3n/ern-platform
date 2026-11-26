<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** A member's request to end the membership, to each admin (R1-12). Name, address, member since: nothing more. */
class MembershipEndRequested extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $member) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'A member asks to end their membership');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.membership-end-requested', text: 'mail.membership-end-requested-text');
    }
}

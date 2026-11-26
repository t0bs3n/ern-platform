<?php

namespace App\Mail;

use App\Members\ActivationLink;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** The welcome e-mail after approval (R1-10): the first name and the link to set a password, nothing else. */
class WelcomeMember extends Mailable
{
    use Queueable, SerializesModels;

    public string $url;

    public int $days;

    public function __construct(public User $member)
    {
        $this->url = ActivationLink::for($member);
        $this->days = (int) config('ern.activation_days', 7);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome to the Esports Research Network');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.welcome-member', text: 'mail.welcome-member-text');
    }
}

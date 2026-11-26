<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The link to set a new password. It holds the member's first name and the link, nothing else.
 */
class PasswordResetLink extends Mailable
{
    use Queueable, SerializesModels;

    public string $url;

    public int $minutes;

    public function __construct(public User $user, string $token)
    {
        $this->url = route('password.reset', ['token' => $token, 'email' => $user->email]);
        $this->minutes = (int) config('auth.passwords.users.expire', 60);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Set a new password for the ERN member area');
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.password-reset',
            text: 'mail.password-reset-text',
        );
    }
}

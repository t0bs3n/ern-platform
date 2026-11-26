<?php

namespace App\Mail;

use App\Models\MembershipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** The answer to a declined application (R1-10). No reason, no name of who decided. */
class ApplicationDeclined extends Mailable
{
    use Queueable, SerializesModels;

    public string $firstName;

    public int $days;

    public function __construct(MembershipApplication $application)
    {
        // Only the first name is kept on the mail, so nothing else of the application travels with it.
        $this->firstName = $application->first_name;
        $this->days = MembershipApplication::KEEP_DECLINED_DAYS;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your application to the Esports Research Network');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.application-declined', text: 'mail.application-declined-text');
    }
}

<?php

namespace App\Mail;

use App\Models\MembershipApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The copy of an application, to the applicant only. No approval time, no links with tokens.
 */
class ApplicationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public MembershipApplication $application) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your application to the Esports Research Network');
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.application-received',
            text: 'mail.application-received-text',
        );
    }
}

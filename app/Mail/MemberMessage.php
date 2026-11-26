<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * A message from one member to another through the directory (R1-13). Sent at once, never queued,
 * so the text is stored nowhere. From the platform; replies go to the sender.
 */
class MemberMessage extends Mailable
{
    use Queueable;

    public string $senderName;

    public string $senderFirstName;

    public ?string $senderPage;

    public string $recipientFirstName;

    public function __construct(User $sender, User $recipient, public string $body)
    {
        $this->senderName = $sender->full_name;
        $this->senderFirstName = $sender->first_name;
        $this->senderPage = $sender->listed_in_directory && $sender->isApproved() ? route('members.show', $sender->slug) : null;
        $this->recipientFirstName = $recipient->first_name;
        $this->replyTo(new Address($sender->email, $sender->full_name));
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->senderName.' wrote to you through ERN');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.member-message', text: 'mail.member-message-text');
    }
}

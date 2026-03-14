<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BuyerRequestMoreInfoMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public User $user,
        public string $message,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'HANZO – Additional information needed for your account');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.buyer-request-more-info');
    }
}

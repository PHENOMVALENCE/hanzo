<?php

namespace App\Mail;

use App\Models\FactoryInvitation;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FactoryInvitationMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public User $user,
        public FactoryInvitation $invitation,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'re invited to join HANZO – East Africa\'s leading B2B trade platform',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.factory-invitation');
    }
}

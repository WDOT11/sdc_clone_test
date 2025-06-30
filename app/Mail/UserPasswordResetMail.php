<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl;

    /**
     * Accept the reset URL when creating the mailable.
     */
    public function __construct($resetUrl)
    {
        $this->resetUrl = $resetUrl;
    }

    /**
     * Set the email subject.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Password Reset Link'
        );
    }

    /**
     * Set the email view and pass data.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.user_password_reset',
            with: [
                'resetUrl' => $this->resetUrl,
            ]
        );
    }

    /**
     * No attachments.
     */
    public function attachments(): array
    {
        return [];
    }
}

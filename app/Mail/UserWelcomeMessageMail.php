<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserWelcomeMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $loginUrl;
    public string $userName;
    public string $userPassword;
    /**
     * Create a new message instance.
     */
    public function __construct(string $loginUrl, string $userName, string $userPassword)
    {
        $this->loginUrl     = $loginUrl;
        $this->userName     = $userName;
        $this->userPassword = $userPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Smart Tech Insurance, ' . $this->userName . '!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.user_welcome_message_mail',
            with: [
                'loginUrl'     => $this->loginUrl,
                'userName'     => $this->userName,
                'tempPassword' => $this->userPassword,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

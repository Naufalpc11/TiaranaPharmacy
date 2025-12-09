<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $token
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password - Tiarana Pharmacy',
        );
    }

    public function content(): Content
    {
        $resetUrl = URL::temporarySignedRoute(
            'password.reset',
            now()->addHours(24),
            ['token' => $this->token, 'email' => $this->user->email]
        );

        return new Content(
            view: 'mail.reset-password',
            with: [
                'user' => $this->user,
                'resetUrl' => $resetUrl,
            ]
        );
    }
}

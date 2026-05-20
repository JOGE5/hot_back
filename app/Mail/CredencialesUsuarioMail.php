<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CredencialesUsuarioMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $passwordTemporal,
        public string $urlAcceso,
    ) {
        $this->user->loadMissing('role');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Credenciales de acceso - Hotel La Mansión',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.credenciales-usuario',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

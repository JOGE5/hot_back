<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CodigoCambioPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $codigo,
        public int $vigenciaMinutos = 15,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Código de verificación - Hotel La Mansión',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.codigo-cambio-password',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

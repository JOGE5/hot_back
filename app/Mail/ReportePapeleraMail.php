<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportePapeleraMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $modulo,
        public string $pdfData,
        public string $nombreArchivo,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reporte de bajas lógicas - ' . $this->modulo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reporte-papelera',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => $this->pdfData,
                $this->nombreArchivo,
            )->withMime('application/pdf'),
        ];
    }
}

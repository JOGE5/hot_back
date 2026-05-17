<?php

namespace App\Mail;

use App\Models\Reservacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReciboReservacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public Reservacion $reservacion;
    public string $pdfData;
    public string $nombreArchivo;

    /**
     * Crear una nueva instancia del Mailable.
     *
     * @param Reservacion $reservacion   La reservación a notificar.
     * @param string      $pdfData       Contenido binario del PDF generado en memoria.
     * @param string      $nombreArchivo Nombre del archivo adjunto.
     */
    public function __construct(Reservacion $reservacion, string $pdfData, string $nombreArchivo)
    {
        $this->reservacion   = $reservacion;
        $this->pdfData       = $pdfData;
        $this->nombreArchivo = $nombreArchivo;
    }

    /** Asunto y remitente del correo. */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recibo de reservación - Hotel La Mansión',
        );
    }

    /** Vista y datos del cuerpo del correo. */
    public function content(): Content
    {
        return new Content(
            view: 'emails.recibo_reservacion',
        );
    }

    /** Adjuntos del correo (PDF generado en memoria). */
    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => $this->pdfData,
                $this->nombreArchivo
            )->withMime('application/pdf'),
        ];
    }
}

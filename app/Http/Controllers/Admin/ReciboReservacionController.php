<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ReciboReservacionMail;
use App\Models\Reservacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ReciboReservacionController extends Controller
{
    /**
     * Descargar el recibo de reservación en PDF.
     */
    public function generarPdf(Request $request, Reservacion $reservacion)
    {
        // Reglas estrictas: estado_pago = Confirmado, total > 0, codigo_checkin existe
        if ($reservacion->estado_pago !== 'Confirmado' || $reservacion->total <= 0 || empty($reservacion->codigo_checkin)) {
            return redirect()->back()->with('error', 'La reservación no cumple con los requisitos para generar un recibo.');
        }

        // Cargar relaciones necesarias si no están cargadas
        $reservacion->loadMissing(['huesped', 'habitacion']);

        $pdf = Pdf::loadView('reportes.recibo_reservacion_pdf', compact('reservacion'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('recibo_reservacion_' . $reservacion->codigo_checkin . '.pdf');
    }

    /**
     * Enviar el recibo de reservación al correo del huésped.
     *
     * Condiciones previas:
     *   - estado_pago = Confirmado
     *   - total > 0
     *   - codigo_checkin no es null
     *   - huesped existe y tiene correo_electronico
     */
    public function enviarCorreo(Reservacion $reservacion)
    {
        // --- Validaciones de negocio ---
        if ($reservacion->estado_pago !== 'Confirmado') {
            return redirect()->back()->with(
                'error',
                'No se puede enviar el recibo. Verifique que la reservación esté pagada y que el huésped tenga correo electrónico.'
            );
        }

        if ($reservacion->total <= 0) {
            return redirect()->back()->with(
                'error',
                'No se puede enviar el recibo. Verifique que la reservación esté pagada y que el huésped tenga correo electrónico.'
            );
        }

        if (empty($reservacion->codigo_checkin)) {
            return redirect()->back()->with(
                'error',
                'No se puede enviar el recibo. Verifique que la reservación esté pagada y que el huésped tenga correo electrónico.'
            );
        }

        // Cargar relaciones si no están disponibles
        $reservacion->loadMissing(['huesped', 'habitacion']);

        if (! $reservacion->huesped) {
            return redirect()->back()->with(
                'error',
                'No se puede enviar el recibo. Verifique que la reservación esté pagada y que el huésped tenga correo electrónico.'
            );
        }

        if (empty($reservacion->huesped->correo_electronico)) {
            return redirect()->back()->with(
                'error',
                'No se puede enviar el recibo. Verifique que la reservación esté pagada y que el huésped tenga correo electrónico.'
            );
        }

        // --- Generar PDF en memoria (sin guardar en disco) ---
        $nombreArchivo = 'recibo_reservacion_' . $reservacion->codigo_checkin . '.pdf';

        $pdfData = Pdf::loadView('reportes.recibo_reservacion_pdf', compact('reservacion'))
                     ->setPaper('a4', 'portrait')
                     ->output(); // contenido binario en memoria

        // --- Enviar correo con el PDF adjunto ---
        try {
            Mail::to($reservacion->huesped->correo_electronico)
                ->send(new ReciboReservacionMail($reservacion, $pdfData, $nombreArchivo));
        } catch (Throwable $e) {
            return redirect()->back()->with(
                'error',
                'No se pudo enviar el recibo. Revise la configuración del correo.'
            );
        }

        return redirect()->back()->with(
            'success',
            'Recibo enviado correctamente al correo del huésped.'
        );
    }
}

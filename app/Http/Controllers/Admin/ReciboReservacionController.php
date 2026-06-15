<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ReciboReservacionMail;
use App\Models\Reservacion;
use App\Support\LogSistema;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ReciboReservacionController extends Controller
{
    /**
     * Generar CUF simulado para fines académicos.
     * Formato: NIT(9) + FECHA(8) + HORA(4) + FACTURA(10) + SUCURSAL(4) + PUNTO_VENTA(7) + HASH(12)
     * Total: 54 caracteres
     */
    private function generarCufSimulado(Reservacion $reservacion): string
    {
        $nit = '000000000'; // NIT de prueba
        $fecha = now()->format('Ymd'); // YYYYMMDD
        $hora = now()->format('Hi'); // HHMM
        $numeroFactura = str_pad($reservacion->id, 10, '0', STR_PAD_LEFT);
        $sucursal = '0001'; // Sucursal por defecto
        $puntoVenta = str_pad(1, 7, '0', STR_PAD_LEFT); // Punto de venta por defecto
        
        // Generar hash usando datos de la reservación
        $datosHash = $nit . $fecha . $hora . $numeroFactura . $reservacion->total . $reservacion->codigo_checkin;
        $hash = strtoupper(substr(hash('sha256', $datosHash), 0, 12));
        
        return $nit . $fecha . $hora . $numeroFactura . $sucursal . $puntoVenta . $hash;
    }

    /**
     * Descargar el recibo de reservación en PDF (FACTURA).
     */
    public function generarPdf(Request $request, Reservacion $reservacion)
    {
        // Reglas estrictas: estado_pago = Confirmado, total > 0, codigo_checkin existe
        if ($reservacion->estado_pago !== 'Confirmado' || $reservacion->total <= 0 || empty($reservacion->codigo_checkin)) {
            return redirect()->back()->with('error', 'La reservación no cumple con los requisitos para generar una factura.');
        }

        // Cargar relaciones necesarias si no están cargadas
        $reservacion->loadMissing(['huesped', 'habitacion']);

        // Generar CUF simulado y número de factura
        $cuf = $this->generarCufSimulado($reservacion);
        $numeroFactura = str_pad($reservacion->id, 6, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('reportes.recibo_reservacion_pdf', compact('reservacion', 'cuf', 'numeroFactura'))
                  ->setPaper([0, 0, 227, 850], 'portrait'); // 80mm x 300mm aprox (80mm = 227px, 300mm = 850px a 72dpi)

        LogSistema::registrar(
            'GENERAR_RECIBO_PDF',
            'Recibos',
            'Factura PDF generada/descargada para reservación #' . $reservacion->id . ' con código ' . $reservacion->codigo_checkin . '.'
        );

        return $pdf->download('factura_' . $reservacion->codigo_checkin . '.pdf');
    }

    /**
     * Enviar la factura de reservación al correo del huésped.
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
                'No se puede enviar la factura. Verifique que la reservación esté pagada y que el huésped tenga correo electrónico.'
            );
        }

        if ($reservacion->total <= 0) {
            return redirect()->back()->with(
                'error',
                'No se puede enviar la factura. Verifique que la reservación esté pagada y que el huésped tenga correo electrónico.'
            );
        }

        if (empty($reservacion->codigo_checkin)) {
            return redirect()->back()->with(
                'error',
                'No se puede enviar la factura. Verifique que la reservación esté pagada y que el huésped tenga correo electrónico.'
            );
        }

        // Cargar relaciones si no están disponibles
        $reservacion->loadMissing(['huesped', 'habitacion']);

        if (! $reservacion->huesped) {
            return redirect()->back()->with(
                'error',
                'No se puede enviar la factura. Verifique que la reservación esté pagada y que el huésped tenga correo electrónico.'
            );
        }

        if (empty($reservacion->huesped->correo_electronico)) {
            return redirect()->back()->with(
                'error',
                'No se puede enviar la factura. Verifique que la reservación esté pagada y que el huésped tenga correo electrónico.'
            );
        }

        // --- Generar PDF en memoria (sin guardar en disco) ---
        $nombreArchivo = 'factura_' . $reservacion->codigo_checkin . '.pdf';
        
        // Generar CUF simulado y número de factura
        $cuf = $this->generarCufSimulado($reservacion);
        $numeroFactura = str_pad($reservacion->id, 6, '0', STR_PAD_LEFT);

        $pdfData = Pdf::loadView('reportes.recibo_reservacion_pdf', compact('reservacion', 'cuf', 'numeroFactura'))
                     ->setPaper([0, 0, 227, 850], 'portrait') // 80mm x 300mm aprox
                     ->output(); // contenido binario en memoria

        // --- Enviar correo con el PDF adjunto ---
        try {
            Mail::to($reservacion->huesped->correo_electronico)
                ->send(new ReciboReservacionMail($reservacion, $pdfData, $nombreArchivo));
        } catch (Throwable $e) {
            return redirect()->back()->with(
                'error',
                'No se pudo enviar la factura. Revise la configuración del correo.'
            );
        }

        LogSistema::registrar(
            'ENVIAR_RECIBO_CORREO',
            'Recibos',
            'Factura enviada por correo a ' . $reservacion->huesped->correo_electronico . ' para reservación #' . $reservacion->id . '.'
        );

        return redirect()->back()->with(
            'success',
            'Factura enviada correctamente al correo del huésped.'
        );
    }
}

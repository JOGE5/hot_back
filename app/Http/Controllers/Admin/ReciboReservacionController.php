<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservacion;
use Barryvdh\DomPDF\Facade\Pdf;

class ReciboReservacionController extends Controller
{
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
}

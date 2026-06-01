<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservacion;
use App\Exports\HistorialCheckInExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;

class CheckInReporteController extends Controller
{
    private function getFilteredQuery(Request $request)
    {
        $query = Reservacion::query()
            ->with([
                'huesped' => fn ($query) => $query->withTrashed(),
                'habitacion' => fn ($query) => $query->withTrashed(),
                'checkinUser' => fn ($query) => $query->withTrashed(),
                'checkoutUser' => fn ($query) => $query->withTrashed(),
            ])
            ->whereNotNull('checkin_at')
            ->whereIn('estado_reservacion', ['En estadía', 'Finalizada']);

        if ($request->filled('buscar')) {
            $busqueda = '%' . $request->buscar . '%';
            $query->where(function (Builder $q) use ($busqueda) {
                $q->where('codigo_checkin', 'like', $busqueda)
                  ->orWhere('codigo_checkout', 'like', $busqueda)
                  ->orWhereHas('huesped', function (Builder $h) use ($busqueda) {
                      $h->withTrashed()
                        ->where('nombres', 'like', $busqueda)
                        ->orWhere('apellido_paterno', 'like', $busqueda)
                        ->orWhere('apellido_materno', 'like', $busqueda)
                        ->orWhere('numero_documento', 'like', $busqueda);
                  })
                  ->orWhereHas('habitacion', function (Builder $hab) use ($busqueda) {
                      $hab->withTrashed()->where('numero', 'like', $busqueda);
                  });
            });
        }

        if ($request->filled('estado_reservacion')) {
            $query->where('estado_reservacion', $request->estado_reservacion);
        }

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('checkin_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('checkin_at', '<=', $request->fecha_hasta);
        }

        if ($request->filled('fecha_checkout_desde')) {
            $query->whereDate('checkout_at', '>=', $request->fecha_checkout_desde);
        }

        if ($request->filled('fecha_checkout_hasta')) {
            $query->whereDate('checkout_at', '<=', $request->fecha_checkout_hasta);
        }

        if ($request->filled('checkin_user_id')) {
            $query->where('checkin_user_id', $request->checkin_user_id);
        }

        if ($request->filled('checkout_user_id')) {
            $query->where('checkout_user_id', $request->checkout_user_id);
        }

        return $query->orderBy('checkin_at', 'desc');
    }

    public function excel(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        return Excel::download(new HistorialCheckInExport($query), 'historial_checkin_' . date('Ymd_His') . '.xlsx');
    }

    public function pdf(Request $request)
    {
        $reservaciones = $this->getFilteredQuery($request)->get();
        
        $filtros = [
            'Buscar' => $request->buscar,
            'Estado' => $request->estado_reservacion,
            'Método Pago' => $request->metodo_pago,
            'IN Desde' => $request->fecha_desde,
            'IN Hasta' => $request->fecha_hasta,
            'OUT Desde' => $request->fecha_checkout_desde,
            'OUT Hasta' => $request->fecha_checkout_hasta,
            'Usuario check-in' => $request->checkin_user_id,
            'Usuario check-out' => $request->checkout_user_id,
        ];

        $pdf = Pdf::loadView('reportes.historial_checkin_pdf', compact('reservaciones', 'filtros'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('historial_checkin_' . date('Ymd_His') . '.pdf');
    }
}

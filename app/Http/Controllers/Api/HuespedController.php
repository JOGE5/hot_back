<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservacion;
use Illuminate\Http\Request;

class HuespedController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user()->load('huesped');

        if (! $user->huesped) {
            return response()->json([
                'message' => 'Perfil de huésped no encontrado.',
            ], 404);
        }

        $huesped = $user->huesped;

        $reservaciones = Reservacion::with('habitacion')
            ->where('huesped_id', $huesped->id)
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'huesped' => [
                'id' => $huesped->id,
                'nombre_completo' => trim($huesped->nombres . ' ' . $huesped->apellido_paterno . ' ' . ($huesped->apellido_materno ?? '')),
                'documento' => $huesped->numero_documento,
                'correo' => $huesped->correo_electronico,
            ],
            'resumen' => [
                'total_reservaciones' => Reservacion::where('huesped_id', $huesped->id)->count(),
                'reservaciones_activas' => Reservacion::where('huesped_id', $huesped->id)
                    ->whereIn('estado_reservacion', ['Pendiente de pago', 'Confirmada', 'En estadía'])
                    ->count(),
            ],
            'ultimas_reservaciones' => $reservaciones,
        ]);
    }
}
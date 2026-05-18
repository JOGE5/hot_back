<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Habitacion;
use App\Models\Menu;
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

    public function habitacionesDisponibles(Request $request)
    {
        $data = $request->validate([
            'fecha_entrada' => ['nullable', 'date', 'required_with:fecha_salida'],
            'fecha_salida' => ['nullable', 'date', 'required_with:fecha_entrada', 'after:fecha_entrada'],
        ]);

        $habitaciones = Habitacion::query()
            ->select([
                'id',
                'numero',
                'tipo',
                'capacidad',
                'precio_noche',
                'estado',
                'descripcion',
            ])
            ->where('estado', 'Disponible')
            ->when(
                ! empty($data['fecha_entrada']) && ! empty($data['fecha_salida']),
                function ($query) use ($data) {
                    $query->whereDoesntHave('reservaciones', function ($reservacionQuery) use ($data) {
                        $reservacionQuery
                            ->whereIn('estado_reservacion', [
                                'Pendiente de pago',
                                'Confirmada',
                                'En estadía',
                            ])
                            ->where('fecha_entrada', '<', $data['fecha_salida'])
                            ->where('fecha_salida', '>', $data['fecha_entrada']);
                    });
                }
            )
            ->orderBy('numero')
            ->get();

        return response()->json([
            'habitaciones' => $habitaciones,
        ]);
    }

    public function misReservaciones(Request $request)
    {
        $user = $request->user()->load('huesped');

        if (! $user->huesped) {
            return response()->json([
                'message' => 'Perfil de huesped no encontrado.',
            ], 404);
        }

        $reservaciones = Reservacion::query()
            ->select([
                'id',
                'huesped_id',
                'habitacion_id',
                'origen_reservacion',
                'fecha_entrada',
                'fecha_salida',
                'cantidad_personas',
                'total',
                'estado_reservacion',
                'metodo_pago',
                'estado_pago',
            ])
            ->with([
                'habitacion:id,numero,tipo,capacidad,precio_noche,estado,descripcion',
                'pagos:id,reservacion_id,monto,metodo_pago,estado_pago,fecha_pago',
            ])
            ->where('huesped_id', $user->huesped->id)
            ->latest('fecha_entrada')
            ->get();

        return response()->json([
            'reservaciones' => $reservaciones,
        ]);
    }

    public function menuDelDia()
    {
        $menus = Menu::query()
            ->select([
                'id',
                'fecha_menu',
                'tipo_menu',
                'estado',
            ])
            ->with([
                'platos' => function ($query) {
                    $query
                        ->select([
                            'platos.id',
                            'nombre',
                            'descripcion',
                            'categoria',
                            'precio',
                            'imagen',
                            'estado',
                            'tiempo_preparacion',
                        ])
                        ->where('platos.estado', 'Activo')
                        ->orderBy('menu_plato.orden');
                },
            ])
            ->whereDate('fecha_menu', today())
            ->where('estado', 'Publicado')
            ->orderBy('tipo_menu')
            ->get()
            ->groupBy('tipo_menu');

        return response()->json([
            'menus' => $menus,
        ]);
    }
}

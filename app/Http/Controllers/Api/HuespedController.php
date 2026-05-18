<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Habitacion;
use App\Models\Menu;
use App\Models\Reservacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function crearReservacion(Request $request)
    {
        $data = $request->validate([
            'habitacion_id' => ['required', 'exists:habitaciones,id'],
            'fecha_entrada' => ['required', 'date', 'after_or_equal:today'],
            'fecha_salida' => ['required', 'date', 'after:fecha_entrada'],
            'cantidad_personas' => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user()->load('huesped');

        if (! $user->huesped) {
            return response()->json([
                'message' => 'Perfil de huésped no encontrado.',
            ], 404);
        }

        $fechaEntrada = Carbon::parse($data['fecha_entrada'])->startOfDay();
        $fechaSalida = Carbon::parse($data['fecha_salida'])->startOfDay();
        $noches = $fechaEntrada->diffInDays($fechaSalida);

        if ($noches < 1) {
            return response()->json([
                'message' => 'La reservación debe tener al menos una noche.',
            ], 422);
        }

        $reservacion = DB::transaction(function () use ($data, $user, $fechaEntrada, $fechaSalida, $noches) {
            $habitacion = Habitacion::query()
                ->whereKey($data['habitacion_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($habitacion->estado !== 'Disponible') {
                return response()->json([
                    'message' => 'La habitación no está disponible.',
                ], 422);
            }

            if ((int) $data['cantidad_personas'] > (int) $habitacion->capacidad) {
                return response()->json([
                    'message' => 'La cantidad de personas supera la capacidad de la habitación.',
                ], 422);
            }

            $existeSolapamiento = Reservacion::query()
                ->where('habitacion_id', $habitacion->id)
                ->whereIn('estado_reservacion', [
                    'Pendiente de pago',
                    'Confirmada',
                    'En estadía',
                ])
                ->where('fecha_entrada', '<', $fechaSalida->toDateString())
                ->where('fecha_salida', '>', $fechaEntrada->toDateString())
                ->lockForUpdate()
                ->exists();

            if ($existeSolapamiento) {
                return response()->json([
                    'message' => 'La habitación ya tiene una reservación activa en esas fechas.',
                ], 422);
            }

            $total = $noches * (float) $habitacion->precio_noche;

            return Reservacion::create([
                'huesped_id' => $user->huesped->id,
                'habitacion_id' => $habitacion->id,
                'origen_reservacion' => 'Web huésped',
                'fecha_entrada' => $fechaEntrada->toDateString(),
                'fecha_salida' => $fechaSalida->toDateString(),
                'cantidad_personas' => $data['cantidad_personas'],
                'total' => $total,
                'estado_reservacion' => 'Pendiente de pago',
                'estado_pago' => 'Pendiente',
                'metodo_pago' => null,
                'codigo_checkin' => null,
                'observacion' => 'Reservación creada desde panel huésped',
            ])->load('habitacion');
        });

        if ($reservacion instanceof \Illuminate\Http\JsonResponse) {
            return $reservacion;
        }

        return response()->json([
            'message' => 'Reservación creada correctamente.',
            'reservacion' => $reservacion,
        ], 201);
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

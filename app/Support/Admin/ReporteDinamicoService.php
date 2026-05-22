<?php

namespace App\Support\Admin;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReporteDinamicoService
{
    public const COLUMNAS = [
        'huespedes' => [
            'nombres' => 'Nombres',
            'apellido_paterno' => 'Apellido paterno',
            'apellido_materno' => 'Apellido materno',
            'numero_documento' => 'Documento',
            'telefono' => 'Teléfono',
            'correo_electronico' => 'Correo electrónico',
            'nacionalidad' => 'Nacionalidad',
            'estado' => 'Estado',
            'created_at' => 'Fecha de registro',
        ],
        'habitaciones' => [
            'numero' => 'Número',
            'tipo' => 'Tipo',
            'capacidad' => 'Capacidad',
            'precio_noche' => 'Precio por noche',
            'estado' => 'Estado',
            'descripcion' => 'Descripción',
            'created_at' => 'Fecha de registro',
        ],
        'reservaciones' => [
            'codigo_checkin' => 'Código de reserva',
            'huesped' => 'Huésped',
            'habitacion' => 'Habitación',
            'fecha_entrada' => 'Fecha de entrada',
            'fecha_salida' => 'Fecha de salida',
            'cantidad_personas' => 'Cantidad de personas',
            'total' => 'Total',
            'estado_reservacion' => 'Estado de reservación',
            'estado_pago' => 'Estado de pago',
            'created_at' => 'Fecha de registro',
        ],
        'usuarios' => [
            'nombres' => 'Nombres',
            'apellido_paterno' => 'Apellido paterno',
            'apellido_materno' => 'Apellido materno',
            'name' => 'Nombre completo',
            'email' => 'Correo electrónico',
            'role' => 'Rol',
            'estado' => 'Estado',
            'created_at' => 'Fecha de registro',
        ],
    ];

    public function columnasPermitidas(string $modulo): array
    {
        return self::COLUMNAS[$modulo] ?? [];
    }

    public function columnasSeleccionadas(string $modulo, array $columnas): array
    {
        $permitidas = $this->columnasPermitidas($modulo);

        return collect($columnas)
            ->filter(fn (string $columna): bool => array_key_exists($columna, $permitidas))
            ->mapWithKeys(fn (string $columna): array => [$columna => $permitidas[$columna]])
            ->all();
    }

    public function filas(string $modulo, Collection $registros, array $columnas): Collection
    {
        return $registros->map(function ($registro) use ($modulo, $columnas): array {
            return collect(array_keys($columnas))
                ->mapWithKeys(fn (string $columna): array => [
                    $columna => $this->valor($modulo, $registro, $columna),
                ])
                ->all();
        });
    }

    public function valor(string $modulo, mixed $registro, string $columna): string
    {
        return match ($modulo) {
            'huespedes' => $this->valorHuesped($registro, $columna),
            'habitaciones' => $this->valorHabitacion($registro, $columna),
            'reservaciones' => $this->valorReservacion($registro, $columna),
            'usuarios' => $this->valorUsuario($registro, $columna),
            default => '',
        };
    }

    private function valorHuesped(mixed $huesped, string $columna): string
    {
        return match ($columna) {
            'estado' => $huesped->estado ? 'Activo' : 'Inactivo',
            'created_at' => $this->formatearFechaHora($huesped->created_at),
            default => (string) ($huesped->{$columna} ?? ''),
        };
    }

    private function valorHabitacion(mixed $habitacion, string $columna): string
    {
        return match ($columna) {
            'precio_noche' => number_format((float) $habitacion->precio_noche, 2, '.', ''),
            'created_at' => $this->formatearFechaHora($habitacion->created_at),
            default => (string) ($habitacion->{$columna} ?? ''),
        };
    }

    private function valorReservacion(mixed $reservacion, string $columna): string
    {
        return match ($columna) {
            'huesped' => $this->nombreHuesped($reservacion),
            'habitacion' => $this->habitacion($reservacion),
            'fecha_entrada' => $this->formatearFecha($reservacion->fecha_entrada),
            'fecha_salida' => $this->formatearFecha($reservacion->fecha_salida),
            'total' => number_format((float) $reservacion->total, 2, '.', ''),
            'created_at' => $this->formatearFechaHora($reservacion->created_at),
            default => (string) ($reservacion->{$columna} ?? ''),
        };
    }

    private function valorUsuario(mixed $usuario, string $columna): string
    {
        return match ($columna) {
            'role' => (string) ($usuario->role?->nombre ?? 'Sin rol'),
            'estado' => $usuario->estado ? 'Activo' : 'Inactivo',
            'created_at' => $this->formatearFechaHora($usuario->created_at),
            default => (string) ($usuario->{$columna} ?? ''),
        };
    }

    private function nombreHuesped(mixed $reservacion): string
    {
        $huesped = $reservacion->huesped;

        if (! $huesped) {
            return 'Sin huésped';
        }

        return trim($huesped->nombres . ' ' . $huesped->apellido_paterno . ' ' . ($huesped->apellido_materno ?? ''));
    }

    private function habitacion(mixed $reservacion): string
    {
        $habitacion = $reservacion->habitacion;

        if (! $habitacion) {
            return 'Sin habitación';
        }

        return trim($habitacion->numero . ' - ' . $habitacion->tipo);
    }

    private function formatearFecha(mixed $fecha): string
    {
        return $fecha ? Carbon::parse($fecha)->format('d/m/Y') : '';
    }

    private function formatearFechaHora(mixed $fecha): string
    {
        return $fecha ? Carbon::parse($fecha)->format('d/m/Y H:i') : '';
    }
}

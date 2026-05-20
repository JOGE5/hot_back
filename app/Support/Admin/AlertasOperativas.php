<?php

namespace App\Support\Admin;

use App\Models\Ingrediente;
use App\Models\Menu;
use App\Models\Paquete;
use App\Models\Reservacion;
use App\Models\Tour;
use App\Models\User;

class AlertasOperativas
{
    /**
     * @return array<int, array{key: string, titulo: string, descripcion: string, total: int, color: string, roles: array<int, string>}>
     */
    public static function paraUsuario(?User $user): array
    {
        $rol = $user?->role?->nombre;

        if (! $rol) {
            return [];
        }

        return array_values(array_filter(self::todas(), function (array $alerta) use ($rol): bool {
            return in_array($rol, $alerta['roles'], true);
        }));
    }

    public static function totalParaUsuario(?User $user): int
    {
        return collect(self::paraUsuario($user))->sum('total');
    }

    /**
     * @return array<int, array{key: string, titulo: string, descripcion: string, total: int, color: string, roles: array<int, string>}>
     */
    private static function todas(): array
    {
        $hoy = today();
        $rolesTodos = ['SUPER ADMIN', 'ADMIN'];
        $rolesRecepcion = ['SUPER ADMIN', 'ADMIN', 'RECEPCIONISTA'];
        $rolesCocina = ['SUPER ADMIN', 'ADMIN', 'CHEF'];

        $reservacionesPendientesPago = Reservacion::query()
            ->where('estado_pago', 'Pendiente')
            ->count();

        $checkinsPendientesHoy = Reservacion::query()
            ->whereDate('fecha_entrada', $hoy)
            ->where('estado_reservacion', 'Confirmada')
            ->whereNull('checkin_at')
            ->count();

        $checkoutsPendientesHoy = Reservacion::query()
            ->whereDate('fecha_salida', $hoy)
            ->where('estado_reservacion', 'En estadía')
            ->whereNull('checkout_at')
            ->count();

        $ingredientesBajoStock = Ingrediente::query()
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->where('stock_actual', '>', 0)
            ->count();

        $menuPublicadoHoy = Menu::query()
            ->whereDate('fecha_menu', $hoy)
            ->where('estado', 'Publicado')
            ->exists();

        $toursCuposBajos = Tour::query()
            ->where('cupos_disponibles', '<=', 3)
            ->where('estado', 'Disponible')
            ->count();

        $paquetesBorrador = Paquete::query()
            ->where('estado', 'Borrador')
            ->count();

        return [
            [
                'key' => 'reservaciones_pendientes_pago',
                'titulo' => 'Reservaciones pendientes de pago',
                'descripcion' => 'Reservaciones con estado de pago Pendiente.',
                'total' => $reservacionesPendientesPago,
                'color' => 'warning',
                'roles' => $rolesRecepcion,
            ],
            [
                'key' => 'checkins_pendientes_hoy',
                'titulo' => 'Check-ins pendientes de hoy',
                'descripcion' => 'Reservaciones confirmadas con entrada para hoy sin check-in registrado.',
                'total' => $checkinsPendientesHoy,
                'color' => 'info',
                'roles' => $rolesRecepcion,
            ],
            [
                'key' => 'checkouts_pendientes_hoy',
                'titulo' => 'Check-outs pendientes de hoy',
                'descripcion' => 'Reservaciones en estadía con salida para hoy sin check-out registrado.',
                'total' => $checkoutsPendientesHoy,
                'color' => 'info',
                'roles' => $rolesRecepcion,
            ],
            [
                'key' => 'ingredientes_bajo_stock',
                'titulo' => 'Ingredientes bajo stock',
                'descripcion' => 'Ingredientes por debajo o igual al stock mínimo.',
                'total' => $ingredientesBajoStock,
                'color' => 'danger',
                'roles' => $rolesCocina,
            ],
            [
                'key' => 'menus_no_publicados_hoy',
                'titulo' => 'Menús no publicados hoy',
                'descripcion' => 'No existe un menú publicado para la fecha de hoy.',
                'total' => $menuPublicadoHoy ? 0 : 1,
                'color' => 'warning',
                'roles' => $rolesCocina,
            ],
            [
                'key' => 'tours_cupos_bajos',
                'titulo' => 'Tours con cupos bajos',
                'descripcion' => 'Tours disponibles con 3 cupos o menos.',
                'total' => $toursCuposBajos,
                'color' => 'warning',
                'roles' => $rolesTodos,
            ],
            [
                'key' => 'paquetes_borrador',
                'titulo' => 'Paquetes en borrador',
                'descripcion' => 'Paquetes turísticos todavía no publicados.',
                'total' => $paquetesBorrador,
                'color' => 'info',
                'roles' => $rolesTodos,
            ],
        ];
    }
}

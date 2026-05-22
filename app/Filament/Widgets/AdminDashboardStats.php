<?php

namespace App\Filament\Widgets;

use App\Models\Habitacion;
use App\Models\Huesped;
use App\Models\Ingrediente;
use App\Models\Menu;
use App\Models\Paquete;
use App\Models\Pago;
use App\Models\Plato;
use App\Models\Reservacion;
use App\Models\Tour;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class AdminDashboardStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Indicadores principales';

    protected ?string $description = 'Resumen operativo para administración.';

    protected int | array | null $columns = [
        'default' => 1,
        'md' => 3,
        'xl' => 4,
    ];

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, [
            'SUPER ADMIN',
            'ADMIN',
            'RECEPCIONISTA',
            'CHEF',
        ], true);
    }

    protected function getStats(): array
    {
        $rol = Filament::auth()->user()?->role?->nombre;

        return match ($rol) {
            'SUPER ADMIN' => $this->metricasSuperAdmin(),
            'ADMIN' => $this->metricasAdmin(),
            'RECEPCIONISTA' => $this->metricasRecepcionista(),
            'CHEF' => $this->metricasChef(),
            default => [],
        };
    }

    private function metricasSuperAdmin(): array
    {
        return [
            $this->statUsuariosActivos(),
            ...$this->metricasAdmin(),
        ];
    }

    private function metricasAdmin(): array
    {
        return [
            $this->statTotalHuespedes(),
            $this->statHabitacionesDisponibles(),
            $this->statHabitacionesOcupadasReservadas(),
            $this->statReservacionesActivas(),
            $this->statPagosPendientes(),
            $this->statIngresosMes(),
            $this->statIngredientesBajoStock(),
            $this->statMenusPublicados(),
            $this->statToursDisponibles(),
            $this->statPaquetesPublicados(),
        ];
    }

    private function metricasRecepcionista(): array
    {
        return [
            $this->statHabitacionesDisponibles(),
            $this->statHabitacionesOcupadasReservadas(),
            $this->statReservacionesActivas(),
            $this->statCheckinsPendientesHoy(),
            $this->statCheckoutsPendientesHoy(),
            $this->statPagosPendientes(),
            $this->statTotalHuespedes('Huéspedes registrados'),
        ];
    }

    private function metricasChef(): array
    {
        return [
            $this->statIngredientesRegistrados(),
            $this->statIngredientesBajoStock(),
            $this->statPlatosDisponibles(),
            $this->statPlatosNoDisponibles(),
            $this->statMenusPublicadosHoy(),
            $this->statMenusBorrador(),
            $this->statPlatosMenuDia(),
        ];
    }

    private function statUsuariosActivos(): Stat
    {
        return Stat::make('Total usuarios activos', User::query()
            ->where('estado', true)
            ->count())
            ->description('Usuarios habilitados en el sistema')
            ->color('primary')
            ->icon('heroicon-o-users')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-primary',
            ]);
    }

    private function statTotalHuespedes(string $label = 'Total huéspedes'): Stat
    {
        return Stat::make($label, Huesped::query()->count())
            ->description('Huéspedes registrados en el sistema')
            ->color('primary')
            ->icon('heroicon-o-user-group')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-primary',
            ]);
    }

    private function statHabitacionesDisponibles(): Stat
    {
        return Stat::make('Habitaciones disponibles', Habitacion::query()
            ->where('estado', 'Disponible')
            ->count())
            ->description('Disponibles para reservar')
            ->color('success')
            ->icon('heroicon-o-home-modern')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-success',
            ]);
    }

    private function statHabitacionesOcupadasReservadas(): Stat
    {
        return Stat::make('Habitaciones ocupadas/reservadas', Habitacion::query()
            ->where(function ($query): void {
                $query
                    ->whereIn('estado', ['Ocupada', 'Reservada'])
                    ->orWhereHas('reservaciones', fn ($query) => $query->bloqueantesDisponibilidad());
            })
            ->count())
            ->description('Con ocupación o reservación activa')
            ->color('danger')
            ->icon('heroicon-o-lock-closed')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-danger',
            ]);
    }

    private function statReservacionesActivas(): Stat
    {
        return Stat::make('Reservaciones activas', Reservacion::query()
            ->bloqueantesDisponibilidad()
            ->count())
            ->description('Pendientes, confirmadas y en estadía')
            ->color('info')
            ->icon('heroicon-o-calendar-days')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-info',
            ]);
    }

    private function statPagosPendientes(): Stat
    {
        return Stat::make('Pagos pendientes', Reservacion::query()
            ->where('estado_pago', 'Pendiente')
            ->count())
            ->description('Reservaciones con pago pendiente')
            ->color('warning')
            ->icon('heroicon-o-clock')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-orange',
            ]);
    }

    private function statIngresosMes(): Stat
    {
        $hoy = Carbon::today();
        $ingresosConfirmadosMes = Pago::query()
            ->where('estado_pago', 'Confirmado')
            ->whereBetween('fecha_pago', [$hoy->copy()->startOfMonth(), $hoy->copy()->endOfMonth()])
            ->sum('monto');

        return Stat::make('Ingresos del mes', $this->formatearBolivianos($ingresosConfirmadosMes))
            ->description('Pagos confirmados del mes actual')
            ->color('warning')
            ->icon('heroicon-o-banknotes')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-gold',
            ]);
    }

    private function statIngredientesBajoStock(): Stat
    {
        return Stat::make('Ingredientes bajo stock', Ingrediente::query()
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->where('stock_actual', '>', 0)
            ->count())
            ->description('Stock mayor a cero y bajo el mínimo')
            ->color('danger')
            ->icon('heroicon-o-exclamation-triangle')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-danger',
            ]);
    }

    private function statMenusPublicados(): Stat
    {
        return Stat::make('Menús publicados', Menu::query()
            ->where('estado', 'Publicado')
            ->count())
            ->description('Menús publicados en el sistema')
            ->color('success')
            ->icon('heroicon-o-book-open')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-success',
            ]);
    }

    private function statToursDisponibles(): Stat
    {
        return Stat::make('Tours disponibles', Tour::query()
            ->where('estado', 'Disponible')
            ->count())
            ->description('Tours con estado disponible')
            ->color('success')
            ->icon('heroicon-o-map')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-success',
            ]);
    }

    private function statPaquetesPublicados(): Stat
    {
        return Stat::make('Paquetes publicados', Paquete::query()
            ->where('estado', 'Publicado')
            ->count())
            ->description('Paquetes actualmente publicados')
            ->color('primary')
            ->icon('heroicon-o-archive-box')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-primary',
            ]);
    }

    private function statCheckinsPendientesHoy(): Stat
    {
        return Stat::make('Check-ins pendientes de hoy', Reservacion::query()
            ->whereDate('fecha_entrada', Carbon::today())
            ->where('estado_reservacion', 'Confirmada')
            ->whereNull('checkin_at')
            ->count())
            ->description('Reservaciones confirmadas para ingresar hoy')
            ->color('info')
            ->icon('heroicon-o-key')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-blue',
            ]);
    }

    private function statCheckoutsPendientesHoy(): Stat
    {
        return Stat::make('Check-outs pendientes de hoy', Reservacion::query()
            ->whereDate('fecha_salida', Carbon::today())
            ->where('estado_reservacion', 'En estadía')
            ->whereNull('checkout_at')
            ->count())
            ->description('Estadías con salida programada para hoy')
            ->color('warning')
            ->icon('heroicon-o-arrow-right-on-rectangle')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-orange',
            ]);
    }

    private function statIngredientesRegistrados(): Stat
    {
        return Stat::make('Ingredientes registrados', Ingrediente::query()->count())
            ->description('Materia prima registrada')
            ->color('primary')
            ->icon('heroicon-o-cube')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-primary',
            ]);
    }

    private function statPlatosDisponibles(): Stat
    {
        return Stat::make('Platos disponibles', Plato::query()
            ->where('estado', 'Disponible')
            ->count())
            ->description('Platos habilitados para el menú')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-success',
            ]);
    }

    private function statPlatosNoDisponibles(): Stat
    {
        return Stat::make('Platos no disponibles', Plato::query()
            ->where('estado', 'No disponible')
            ->count())
            ->description('Platos deshabilitados temporalmente')
            ->color('warning')
            ->icon('heroicon-o-no-symbol')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-orange',
            ]);
    }

    private function statMenusPublicadosHoy(): Stat
    {
        return Stat::make('Menús publicados hoy', Menu::query()
            ->whereDate('fecha_menu', Carbon::today())
            ->where('estado', 'Publicado')
            ->count())
            ->description('Menús publicados para la fecha actual')
            ->color('success')
            ->icon('heroicon-o-calendar')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-success',
            ]);
    }

    private function statMenusBorrador(): Stat
    {
        return Stat::make('Menús en borrador', Menu::query()
            ->where('estado', 'Borrador')
            ->count())
            ->description('Menús pendientes de publicación')
            ->color('warning')
            ->icon('heroicon-o-pencil-square')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-orange',
            ]);
    }

    private function statPlatosMenuDia(): Stat
    {
        return Stat::make('Platos asociados al menú del día', Plato::query()
            ->whereHas('menus', fn ($query) => $query->whereDate('fecha_menu', Carbon::today()))
            ->distinct()
            ->count('platos.id'))
            ->description('Platos vinculados a menús de hoy')
            ->color('info')
            ->icon('heroicon-o-list-bullet')
            ->extraAttributes([
                'class' => 'admin-kpi-card admin-kpi-card-info',
            ]);
    }

    private function formatearBolivianos(float|int|string|null $monto): string
    {
        return 'Bs. ' . number_format((float) $monto, 2, '.', ',');
    }
}

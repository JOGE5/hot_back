<?php

namespace App\Filament\Widgets;

use App\Models\Habitacion;
use App\Models\Ingrediente;
use App\Models\Paquete;
use App\Models\Pago;
use App\Models\Reservacion;
use App\Models\Tour;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class AdminDashboardStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Indicadores principales';

    protected ?string $description = 'Resumen operativo para administracion.';

    protected int | array | null $columns = [
        'default' => 1,
        'md' => 3,
        'xl' => 4,
    ];

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, ['ADMIN', 'SUPER ADMIN'], true);
    }

    protected function getStats(): array
    {
        $hoy = Carbon::today();
        $inicioMes = $hoy->copy()->startOfMonth();
        $finMes = $hoy->copy()->endOfMonth();

        $ingresosConfirmadosMes = Pago::query()
            ->where('estado_pago', 'Confirmado')
            ->whereBetween('fecha_pago', [$inicioMes, $finMes])
            ->sum('monto');

        return [
            Stat::make('Reservaciones activas', Reservacion::query()
                ->whereIn('estado_reservacion', ['Pendiente de pago', 'Confirmada', 'En estadía'])
                ->count())
                ->description('Pendientes, confirmadas y en estadia')
                ->color('info')
                ->icon('heroicon-o-calendar-days')
                ->extraAttributes([
                    'class' => 'admin-kpi-card admin-kpi-card-info',
                ]),

            Stat::make('Ingresos confirmados del mes', $this->formatearBolivianos($ingresosConfirmadosMes))
                ->description('Pagos confirmados del mes actual')
                ->color('warning')
                ->icon('heroicon-o-banknotes')
                ->extraAttributes([
                    'class' => 'admin-kpi-card admin-kpi-card-gold',
                ]),

            Stat::make('Pagos pendientes', Reservacion::query()
                ->where('estado_pago', 'Pendiente')
                ->count())
                ->description('Reservaciones con pago pendiente')
                ->color('warning')
                ->icon('heroicon-o-clock')
                ->extraAttributes([
                    'class' => 'admin-kpi-card admin-kpi-card-orange',
                ]),

            Stat::make('Habitaciones disponibles', Habitacion::query()
                ->where('estado', 'Disponible')
                ->count())
                ->description('Disponibles para reservar')
                ->color('success')
                ->icon('heroicon-o-home-modern')
                ->extraAttributes([
                    'class' => 'admin-kpi-card admin-kpi-card-success',
                ]),

            Stat::make('Tours disponibles', Tour::query()
                ->where('estado', 'Disponible')
                ->count())
                ->description('Tours con estado disponible')
                ->color('success')
                ->icon('heroicon-o-map')
                ->extraAttributes([
                    'class' => 'admin-kpi-card admin-kpi-card-success',
                ]),

            Stat::make('Paquetes publicados', Paquete::query()
                ->where('estado', 'Publicado')
                ->count())
                ->description('Paquetes actualmente publicados')
                ->color('primary')
                ->icon('heroicon-o-archive-box')
                ->extraAttributes([
                    'class' => 'admin-kpi-card admin-kpi-card-primary',
                ]),

            Stat::make('Check-ins pendientes de hoy', Reservacion::query()
                ->whereDate('fecha_entrada', $hoy)
                ->where('estado_reservacion', 'Confirmada')
                ->whereNull('checkin_at')
                ->count())
                ->description('Reservaciones confirmadas para ingresar hoy')
                ->color('info')
                ->icon('heroicon-o-key')
                ->extraAttributes([
                    'class' => 'admin-kpi-card admin-kpi-card-blue',
                ]),

            Stat::make('Ingredientes bajo stock', Ingrediente::query()
                ->whereColumn('stock_actual', '<=', 'stock_minimo')
                ->where('stock_actual', '>', 0)
                ->count())
                ->description('Stock mayor a cero y bajo el minimo')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle')
                ->extraAttributes([
                    'class' => 'admin-kpi-card admin-kpi-card-danger',
                ]),
        ];
    }

    private function formatearBolivianos(float|int|string|null $monto): string
    {
        return 'Bs. ' . number_format((float) $monto, 2, '.', ',');
    }
}

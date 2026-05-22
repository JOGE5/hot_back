<?php

namespace App\Filament\Widgets;

use App\Models\Reservacion;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;

class ReservacionesEstadoChart extends ChartWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    protected ?string $heading = 'Reservaciones por estado';

    protected ?string $description = 'Estado operativo de las reservaciones.';

    protected string $color = 'info';

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, ['SUPER ADMIN', 'ADMIN', 'RECEPCIONISTA'], true);
    }

    protected function getData(): array
    {
        $estados = ['Pendiente de pago', 'Confirmada', 'En estadía', 'Finalizada', 'Cancelada'];

        return [
            'datasets' => [
                [
                    'label' => 'Reservaciones',
                    'data' => collect($estados)->map(fn (string $estado): int => Reservacion::query()
                        ->where('estado_reservacion', $estado)
                        ->count())->all(),
                    'backgroundColor' => ['#f59e0b', '#38bdf8', '#22c55e', '#64748b', '#ef4444'],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $estados,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

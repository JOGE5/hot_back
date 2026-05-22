<?php

namespace App\Filament\Widgets;

use App\Models\Habitacion;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;

class HabitacionesEstadoChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    protected ?string $heading = 'Habitaciones por estado';

    protected ?string $description = 'Distribucion actual del inventario hotelero.';

    protected string $color = 'success';

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, ['SUPER ADMIN', 'ADMIN', 'RECEPCIONISTA'], true);
    }

    protected function getData(): array
    {
        $estados = ['Disponible', 'Ocupada', 'Mantenimiento', 'Limpieza', 'Inactiva'];

        return [
            'datasets' => [
                [
                    'label' => 'Habitaciones',
                    'data' => collect($estados)->map(fn (string $estado): int => Habitacion::query()
                        ->where('estado', $estado)
                        ->count())->all(),
                    'backgroundColor' => ['#22c55e', '#ef4444', '#f59e0b', '#38bdf8', '#64748b'],
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

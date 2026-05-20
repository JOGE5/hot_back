<?php

namespace App\Filament\Widgets;

use App\Models\Tour;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;

class ToursEstadoChart extends ChartWidget
{
    protected static ?int $sort = 8;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    protected ?string $heading = 'Tours por estado';

    protected ?string $description = 'Distribución actual de los tours disponibles.';

    protected string $color = 'success';

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, ['ADMIN', 'SUPER ADMIN'], true);
    }

    protected function getData(): array
    {
        $estados = ['Disponible', 'No disponible', 'Archivado'];

        return [
            'datasets' => [
                [
                    'label' => 'Tours',
                    'data' => collect($estados)->map(fn (string $estado): int => Tour::query()
                        ->where('estado', $estado)
                        ->count())->all(),
                    'backgroundColor' => ['#22c55e', '#f97316', '#64748b'],
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

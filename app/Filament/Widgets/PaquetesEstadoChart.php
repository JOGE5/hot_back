<?php

namespace App\Filament\Widgets;

use App\Models\Paquete;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;

class PaquetesEstadoChart extends ChartWidget
{
    protected static ?int $sort = 9;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    protected ?string $heading = 'Paquetes por estado';

    protected ?string $description = 'Estado de publicación y archivo de paquetes.';

    protected string $color = 'warning';

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, ['ADMIN', 'SUPER ADMIN'], true);
    }

    protected function getData(): array
    {
        $estados = ['Borrador', 'Publicado', 'Archivado'];

        return [
            'datasets' => [
                [
                    'label' => 'Paquetes',
                    'data' => collect($estados)->map(fn (string $estado): int => Paquete::query()
                        ->where('estado', $estado)
                        ->count())->all(),
                    'backgroundColor' => ['#f59e0b', '#2563eb', '#64748b'],
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

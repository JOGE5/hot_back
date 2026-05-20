<?php

namespace App\Filament\Widgets;

use App\Models\Reservacion;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ReservacionesMensualesChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'xl' => 2,
    ];

    protected ?string $heading = 'Reservaciones por mes';

    protected ?string $description = 'Ultimos 12 meses segun fecha de entrada.';

    protected string $color = 'info';

    protected ?string $maxHeight = '360px';

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, ['ADMIN', 'SUPER ADMIN'], true);
    }

    protected function getData(): array
    {
        $meses = collect(range(11, 0))->map(fn (int $mesesAtras): Carbon => now()
            ->subMonths($mesesAtras)
            ->startOfMonth());

        return [
            'datasets' => [
                [
                    'label' => 'Reservaciones',
                    'data' => $meses->map(fn (Carbon $mes): int => Reservacion::query()
                        ->whereBetween('fecha_entrada', [$mes->copy()->startOfMonth(), $mes->copy()->endOfMonth()])
                        ->count())->all(),
                    'borderColor' => '#38bdf8',
                    'backgroundColor' => 'rgba(56, 189, 248, 0.18)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $meses->map(fn (Carbon $mes): string => $mes->translatedFormat('M Y'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Pago;
use Filament\Facades\Filament;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class IngresosMensualesChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    protected ?string $heading = 'Ingresos por mes';

    protected ?string $description = 'Pagos confirmados en los ultimos 12 meses.';

    protected string $color = 'warning';

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
                    'label' => 'Ingresos confirmados',
                    'data' => $meses->map(fn (Carbon $mes): float => (float) Pago::query()
                        ->where('estado_pago', 'Confirmado')
                        ->whereBetween('fecha_pago', [$mes->copy()->startOfMonth(), $mes->copy()->endOfMonth()])
                        ->sum('monto'))->all(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.65)',
                ],
            ],
            'labels' => $meses->map(fn (Carbon $mes): string => $mes->translatedFormat('M Y'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => 'Bs. ' + Number(value).toLocaleString('es-BO', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            }),
                        },
                    },
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (context) => 'Bs. ' + Number(context.parsed.y ?? 0).toLocaleString('es-BO', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            }),
                        },
                    },
                },
            }
            JS);
    }
}

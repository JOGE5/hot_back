<?php

namespace App\Filament\Widgets;

use App\Models\Pago;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;

class MetodosPagoChart extends ChartWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    protected ?string $heading = 'Metodos de pago';

    protected ?string $description = 'Pagos registrados por metodo.';

    protected string $color = 'primary';

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, ['SUPER ADMIN', 'ADMIN', 'RECEPCIONISTA'], true);
    }

    protected function getData(): array
    {
        $metodos = ['QR', 'Efectivo', 'Transferencia', 'Tarjeta'];

        return [
            'datasets' => [
                [
                    'label' => 'Pagos',
                    'data' => collect($metodos)->map(fn (string $metodo): int => Pago::query()
                        ->where('metodo_pago', $metodo)
                        ->count())->all(),
                    'backgroundColor' => ['#22c55e', '#f59e0b', '#38bdf8', '#6366f1'],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $metodos,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}

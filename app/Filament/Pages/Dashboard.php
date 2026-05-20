<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static bool $isDiscovered = false;

    protected static ?string $title = 'Métricas';

    protected static ?string $navigationLabel = 'Métricas';

    public function getColumns(): int | array
    {
        return [
            'default' => 1,
            'lg' => 2,
            'xl' => 3,
        ];
    }

    public function getPageClasses(): array
    {
        return [
            'admin-metrics-dashboard',
        ];
    }
}

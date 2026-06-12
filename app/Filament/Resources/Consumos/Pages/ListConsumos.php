<?php

namespace App\Filament\Resources\Consumos\Pages;

use App\Filament\Resources\Consumos\ConsumoResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConsumos extends ListRecords
{
    protected static string $resource = ConsumoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('platos_populares')
                ->label('Platos populares')
                ->icon('heroicon-o-chart-bar')
                ->color('success')
                ->url(ConsumoResource::getUrl('platos-populares')),

            Action::make('reporte_consumos')
                ->label('Reporte consumos')
                ->icon('heroicon-o-document-text')
                ->color('warning')
                ->url(ConsumoResource::getUrl('reporte')),

            CreateAction::make(),
        ];
    }
}

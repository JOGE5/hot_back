<?php

namespace App\Filament\Resources\Consumos\Pages;

use App\Filament\Resources\Consumos\ConsumoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewConsumo extends ViewRecord
{
    protected static string $resource = ConsumoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar'),

            DeleteAction::make()
                ->label('Eliminar')
                ->visible(fn (): bool => ConsumoResource::canDelete($this->record)),
        ];
    }
}

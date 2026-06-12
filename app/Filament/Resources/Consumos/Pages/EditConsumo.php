<?php

namespace App\Filament\Resources\Consumos\Pages;

use App\Filament\Resources\Consumos\ConsumoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConsumo extends EditRecord
{
    protected static string $resource = ConsumoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar')
                ->visible(fn (): bool => ConsumoResource::canDelete($this->record)),
        ];
    }
}

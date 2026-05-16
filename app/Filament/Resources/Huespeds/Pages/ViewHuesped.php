<?php

namespace App\Filament\Resources\Huespeds\Pages;

use App\Filament\Resources\Huespeds\HuespedResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewHuesped extends ViewRecord
{
    protected static string $resource = HuespedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar')
                ->visible(fn (): bool => HuespedResource::canEdit($this->record)),

            DeleteAction::make()
                ->label('Dar de baja')
                ->modalHeading('Dar de baja huésped')
                ->modalDescription('El huésped será dado de baja lógicamente y podrá recuperarse posteriormente.')
                ->modalSubmitActionLabel('Sí, dar de baja')
                ->successNotificationTitle('Huésped dado de baja correctamente')
                ->visible(fn (): bool => HuespedResource::canDelete($this->record)),
        ];
    }
}
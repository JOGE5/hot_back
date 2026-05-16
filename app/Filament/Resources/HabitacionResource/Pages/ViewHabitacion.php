<?php

namespace App\Filament\Resources\HabitacionResource\Pages;

use App\Filament\Resources\HabitacionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewHabitacion extends ViewRecord
{
    protected static string $resource = HabitacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar')
                ->visible(fn (): bool => HabitacionResource::canEdit($this->getRecord())),
        ];
    }
}

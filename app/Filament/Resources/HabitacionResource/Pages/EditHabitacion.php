<?php

namespace App\Filament\Resources\HabitacionResource\Pages;

use App\Filament\Resources\HabitacionResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditHabitacion extends EditRecord
{
    protected static string $resource = HabitacionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['activo'] = $data['estado'] !== 'Inactiva';
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Ver'),
        ];
    }
}

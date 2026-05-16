<?php

namespace App\Filament\Resources\HabitacionResource\Pages;

use App\Filament\Resources\HabitacionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHabitacion extends CreateRecord
{
    protected static string $resource = HabitacionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['activo'] = $data['estado'] !== 'Inactiva';
        return $data;
    }
}

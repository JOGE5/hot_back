<?php

namespace App\Filament\Resources\Huespeds\Pages;

use App\Filament\Resources\Huespeds\HuespedResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHuesped extends CreateRecord
{
    protected static string $resource = HuespedResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['nacionalidad'] ?? '') === 'Otra') {
            $data['nacionalidad'] = $data['nacionalidad_otra'] ?? null;
        }

        unset($data['nacionalidad_otra']);

        return $data;
    }
}

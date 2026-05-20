<?php

namespace App\Filament\Resources\Huespeds\Pages;

use App\Filament\Resources\Huespeds\HuespedResource;
use App\Support\LogSistema;
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

    protected function afterCreate(): void
    {
        LogSistema::registrar(
            'CREAR',
            'Huéspedes',
            'Huésped creado: ' . trim($this->record->nombres . ' ' . $this->record->apellido_paterno) . ' documento ' . $this->record->numero_documento . '.'
        );
    }
}

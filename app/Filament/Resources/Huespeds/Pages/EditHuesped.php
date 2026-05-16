<?php

namespace App\Filament\Resources\Huespeds\Pages;

use App\Filament\Resources\Huespeds\HuespedResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditHuesped extends EditRecord
{
    protected static string $resource = HuespedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Ver'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $opcionesNacionalidad = [
            'Bolivia', 'Argentina', 'Brasil', 'Chile', 'Colombia', 'Ecuador', 
            'Paraguay', 'Perú', 'Uruguay', 'Venezuela', 'México', 'Estados Unidos', 'España'
        ];
        
        if (isset($data['nacionalidad']) && !in_array($data['nacionalidad'], $opcionesNacionalidad)) {
            $data['nacionalidad_otra'] = $data['nacionalidad'];
            $data['nacionalidad'] = 'Otra';
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['nacionalidad'] ?? '') === 'Otra') {
            $data['nacionalidad'] = $data['nacionalidad_otra'] ?? null;
        }

        unset($data['nacionalidad_otra']);

        return $data;
    }
}
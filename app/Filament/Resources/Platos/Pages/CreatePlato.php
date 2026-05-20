<?php

namespace App\Filament\Resources\Platos\Pages;

use App\Filament\Resources\Platos\PlatoResource;
use App\Support\LogSistema;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePlato extends CreateRecord
{
    protected static string $resource = PlatoResource::class;

    protected array $ingredientesReceta = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->ingredientesReceta = $data['ingredientes_receta'] ?? [];

        unset($data['ingredientes_receta']);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation($data);

        $record->ingredientes()->sync($this->getIngredientesSyncData());

        return $record;
    }

    protected function afterCreate(): void
    {
        LogSistema::registrar(
            'CREAR',
            'Platos',
            'Plato creado: ' . $this->record->nombre . '.'
        );
    }

    protected function getIngredientesSyncData(): array
    {
        $syncData = [];

        foreach ($this->ingredientesReceta as $item) {
            if (empty($item['ingrediente_id']) || empty($item['cantidad_requerida'])) {
                continue;
            }

            $syncData[$item['ingrediente_id']] = [
                'cantidad_requerida' => $item['cantidad_requerida'],
            ];
        }

        return $syncData;
    }
}

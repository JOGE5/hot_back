<?php

namespace App\Filament\Resources\Platos\Pages;

use App\Filament\Resources\Platos\PlatoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPlato extends EditRecord
{
    protected static string $resource = PlatoResource::class;

    protected array $ingredientesReceta = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['ingredientes_receta'] = $this->getRecord()
            ->ingredientes()
            ->get()
            ->map(fn ($ingrediente): array => [
                'ingrediente_id' => $ingrediente->id,
                'cantidad_requerida' => $ingrediente->pivot->cantidad_requerida,
            ])
            ->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->ingredientesReceta = $data['ingredientes_receta'] ?? [];

        unset($data['ingredientes_receta']);

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record = parent::handleRecordUpdate($record, $data);

        $record->ingredientes()->sync($this->getIngredientesSyncData());

        return $record;
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

<?php

namespace App\Filament\Resources\Platos\Pages;

use App\Filament\Resources\Platos\PlatoResource;
use App\Support\LogSistema;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPlato extends EditRecord
{
    protected static string $resource = PlatoResource::class;

    protected array $ingredientesReceta = [];

    protected array $ingredientesRecetaAnterior = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->after(function (): void {
                    LogSistema::registrar(
                        'ELIMINAR',
                        'Platos',
                        'Plato eliminado: ' . $this->record->nombre . '.'
                    );
                }),
            ForceDeleteAction::make(),
            RestoreAction::make()
                ->after(function (): void {
                    LogSistema::registrar(
                        'RESTAURAR',
                        'Platos',
                        'Plato restaurado: ' . $this->record->nombre . '.'
                    );
                }),
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
        $this->ingredientesRecetaAnterior = $this->getIngredientesRecetaActual();
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

    protected function afterSave(): void
    {
        $descripcion = 'Plato actualizado: ' . $this->record->nombre . '.';

        if ($this->ingredientesRecetaAnterior !== $this->normalizarIngredientesReceta($this->ingredientesReceta)) {
            $descripcion .= ' Se modificó receta/ingredientes requeridos.';
        }

        LogSistema::registrar(
            'EDITAR',
            'Platos',
            $descripcion
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

    protected function getIngredientesRecetaActual(): array
    {
        return $this->normalizarIngredientesReceta(
            $this->getRecord()
                ->ingredientes()
                ->get()
                ->map(fn ($ingrediente): array => [
                    'ingrediente_id' => $ingrediente->id,
                    'cantidad_requerida' => $ingrediente->pivot->cantidad_requerida,
                ])
                ->toArray()
        );
    }

    protected function normalizarIngredientesReceta(array $ingredientes): array
    {
        $normalizados = [];

        foreach ($ingredientes as $item) {
            if (empty($item['ingrediente_id']) || empty($item['cantidad_requerida'])) {
                continue;
            }

            $normalizados[(int) $item['ingrediente_id']] = number_format((float) $item['cantidad_requerida'], 2, '.', '');
        }

        ksort($normalizados);

        return $normalizados;
    }
}

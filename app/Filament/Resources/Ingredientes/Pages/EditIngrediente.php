<?php

namespace App\Filament\Resources\Ingredientes\Pages;

use App\Filament\Resources\Ingredientes\IngredienteResource;
use App\Support\LogSistema;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditIngrediente extends EditRecord
{
    protected static string $resource = IngredienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->after(function (): void {
                    LogSistema::registrar(
                        'ELIMINAR',
                        'Ingredientes',
                        'Ingrediente eliminado: ' . $this->record->nombre . '.'
                    );
                }),
            ForceDeleteAction::make(),
            RestoreAction::make()
                ->after(function (): void {
                    LogSistema::registrar(
                        'RESTAURAR',
                        'Ingredientes',
                        'Ingrediente restaurado: ' . $this->record->nombre . '.'
                    );
                }),
        ];
    }

    protected function afterSave(): void
    {
        LogSistema::registrar(
            'EDITAR',
            'Ingredientes',
            'Ingrediente actualizado: ' . $this->record->nombre . ', stock actual ' . $this->record->stock_actual . ' ' . $this->record->unidad_medida . '.'
        );
    }
}

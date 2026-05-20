<?php

namespace App\Filament\Resources\Ingredientes\Pages;

use App\Filament\Resources\Ingredientes\IngredienteResource;
use App\Support\LogSistema;
use Filament\Resources\Pages\CreateRecord;

class CreateIngrediente extends CreateRecord
{
    protected static string $resource = IngredienteResource::class;

    protected function afterCreate(): void
    {
        LogSistema::registrar(
            'CREAR',
            'Ingredientes',
            'Ingrediente creado: ' . $this->record->nombre . ', stock actual ' . $this->record->stock_actual . ' ' . $this->record->unidad_medida . '.'
        );
    }
}

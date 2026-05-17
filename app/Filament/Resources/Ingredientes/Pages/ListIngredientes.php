<?php

namespace App\Filament\Resources\Ingredientes\Pages;

use App\Filament\Resources\Ingredientes\IngredienteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIngredientes extends ListRecords
{
    protected static string $resource = IngredienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

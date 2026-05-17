<?php

namespace App\Filament\Resources\Ingredientes\Pages;

use App\Filament\Resources\Ingredientes\IngredienteResource;
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
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

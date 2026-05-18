<?php

namespace App\Filament\Resources\Platos\Pages;

use App\Filament\Resources\Platos\PlatoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPlato extends EditRecord
{
    protected static string $resource = PlatoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

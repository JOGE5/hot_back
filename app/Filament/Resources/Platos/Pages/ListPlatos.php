<?php

namespace App\Filament\Resources\Platos\Pages;

use App\Filament\Resources\Platos\PlatoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPlatos extends ListRecords
{
    protected static string $resource = PlatoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

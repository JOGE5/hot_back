<?php

namespace App\Filament\Resources\HuespedesEliminados\Pages;

use App\Filament\Resources\HuespedesEliminados\HuespedEliminadoResource;
use Filament\Resources\Pages\ListRecords;

class ListHuespedesEliminados extends ListRecords
{
    protected static string $resource = HuespedEliminadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Sin acciones superiores porque es papelera
        ];
    }
}

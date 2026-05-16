<?php

namespace App\Filament\Resources\UsuariosEliminados\Pages;

use App\Filament\Resources\UsuariosEliminados\UsuarioEliminadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsuariosEliminados extends ListRecords
{
    protected static string $resource = UsuarioEliminadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No creamos acciones de header como 'CreateAction' porque aquí no se crean registros.
        ];
    }
}

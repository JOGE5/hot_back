<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\UsuariosEliminados\UsuarioEliminadoResource;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('papelera_usuarios')
                ->label('Papelera de usuarios')
                ->icon('heroicon-o-trash')
                ->color('gray')
                ->url(UsuarioEliminadoResource::getUrl('index')),

            CreateAction::make()
                ->label('Nuevo usuario administrativo'),
        ];
    }
}
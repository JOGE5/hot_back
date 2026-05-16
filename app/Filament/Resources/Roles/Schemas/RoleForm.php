<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre del rol')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('El nombre del rol es fijo porque la lógica del sistema depende de este valor.'),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->maxLength(500)
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('estado')
                    ->label('Activo')
                    ->required()
                    ->disabled(function ($record): bool {
                        if (! $record) {
                            return true;
                        }

                        $user = Filament::auth()->user();

                        if (! $user?->role) {
                            return true;
                        }

                        if ((int) $user->role_id === (int) $record->id) {
                            return true;
                        }

                        if ($user->role->nombre === 'ADMIN' && $record->nombre === 'SUPER ADMIN') {
                            return true;
                        }

                        return ! in_array($record->nombre, RoleResource::ROLES_ADMINISTRATIVOS, true);
                    }),
            ]);
    }
}
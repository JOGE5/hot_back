<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Resources\Users\UserResource;
use App\Models\Role;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('role_id')
                    ->label('Rol')
                    ->options(function (): array {
                        $user = Filament::auth()->user();

                        if (! $user?->role) {
                            return [];
                        }

                        $rolesPermitidos = match ($user->role->nombre) {
                            'SUPER ADMIN' => UserResource::ROLES_CREABLES_SUPER_ADMIN,
                            'ADMIN' => UserResource::ROLES_CREABLES_ADMIN,
                            default => [],
                        };

                        return Role::query()
                            ->whereIn('nombre', $rolesPermitidos)
                            ->where('estado', true)
                            ->orderBy('nombre')
                            ->pluck('nombre', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->searchable()
                    ->preload(),

                   TextInput::make('nombres')
                        ->label('Nombres')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('apellido_paterno')
                        ->label('Apellido paterno')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('apellido_materno')
                        ->label('Apellido materno')
                        ->nullable()
                        ->maxLength(255),

                TextInput::make('email')
                    ->label('Correo electrónico')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Toggle::make('estado')
                    ->label('Activo')
                    ->required()
                    ->default(true)
                    ->disabled(function ($record): bool {
                        $user = Filament::auth()->user();

                        if (! $record || ! $user) {
                            return false;
                        }

                        return (int) $user->id === (int) $record->id;
                    }),

            ]);
    }
}
<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Correo electrónico')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role.nombre')
                    ->label('Rol')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                IconColumn::make('estado')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('email_verified_at')
                    ->label('Correo verificado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('estado')
                    ->label('Estado')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos')
                    ->native(false),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver'),

                EditAction::make()
                    ->label('Editar')
                    ->visible(fn ($record): bool => UserResource::puedeGestionarUsuario($record, permitirPropio: false)),
            ])
            ->toolbarActions([]);
    }
}
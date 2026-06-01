<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(['nombres', 'apellido_paterno', 'apellido_materno', 'name'])
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
                SelectFilter::make('role_id')
                    ->label('Rol')
                    ->options(fn (): array => \App\Models\Role::query()
                        ->whereIn('nombre', UserResource::ROLES_ADMINISTRATIVOS)
                        ->orderBy('nombre')
                        ->pluck('nombre', 'id')
                        ->all())
                    ->native(false),

                TernaryFilter::make('estado')
                    ->label('Estado')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos')
                    ->native(false),

                Filter::make('created_at')
                    ->label('Fecha de creación')
                    ->form([
                        DatePicker::make('desde')
                            ->label('Desde'),
                        DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                filled($data['desde'] ?? null),
                                fn (Builder $query): Builder => $query->whereDate('created_at', '>=', $data['desde'])
                            )
                            ->when(
                                filled($data['hasta'] ?? null),
                                fn (Builder $query): Builder => $query->whereDate('created_at', '<=', $data['hasta'])
                            );
                    }),
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

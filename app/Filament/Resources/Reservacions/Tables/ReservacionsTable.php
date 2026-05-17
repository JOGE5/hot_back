<?php

namespace App\Filament\Resources\Reservacions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReservacionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('huesped.id')
                    ->searchable(),
                TextColumn::make('habitacion.id')
                    ->searchable(),
                TextColumn::make('origen_reservacion')
                    ->searchable(),
                TextColumn::make('fecha_entrada')
                    ->date()
                    ->sortable(),
                TextColumn::make('fecha_salida')
                    ->date()
                    ->sortable(),
                TextColumn::make('cantidad_personas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('estado_reservacion')
                    ->searchable(),
                TextColumn::make('metodo_pago')
                    ->searchable(),
                TextColumn::make('estado_pago')
                    ->searchable(),
                TextColumn::make('codigo_checkin')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

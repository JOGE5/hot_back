<?php

namespace App\Filament\Resources\Platos\Tables;

use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PlatosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                View::make('filament.resources.platos.card')
                    ->components([
                        TextColumn::make('nombre')
                            ->label('Nombre')
                            ->searchable(),

                        TextColumn::make('categoria')
                            ->label('Categoría')
                            ->searchable(),

                        TextColumn::make('estado')
                            ->label('Estado')
                            ->searchable(),

                        TextColumn::make('chef.name')
                            ->label('Chef')
                            ->searchable(),
                    ]),
            ])
            ->filters([
                TrashedFilter::make(),

                SelectFilter::make('categoria')
                    ->label('Categoría')
                    ->options([
                        'Desayuno' => 'Desayuno',
                        'Almuerzo' => 'Almuerzo',
                        'Cena' => 'Cena',
                        'Bebida' => 'Bebida',
                        'Postre' => 'Postre',
                        'Especial' => 'Especial',
                    ]),

                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'Disponible' => 'Disponible',
                        'No disponible' => 'No disponible',
                        'En preparación' => 'En preparación',
                        'Archivado' => 'Archivado',
                    ]),

                SelectFilter::make('chef_id')
                    ->label('Chef responsable')
                    ->relationship('chef', 'name'),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
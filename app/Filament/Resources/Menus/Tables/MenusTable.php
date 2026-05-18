<?php

namespace App\Filament\Resources\Menus\Tables;

use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                View::make('filament.resources.menus.card')
                    ->components([
                        TextColumn::make('fecha_menu')
                            ->label('Fecha')
                            ->date()
                            ->sortable(),

                        TextColumn::make('tipo_menu')
                            ->label('Tipo')
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

                SelectFilter::make('tipo_menu')
                    ->label('Tipo de menu')
                    ->options([
                        'Desayuno' => 'Desayuno',
                        'Almuerzo' => 'Almuerzo',
                        'Cena' => 'Cena',
                        'Especial del día' => 'Especial del día',
                    ]),

                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'Borrador' => 'Borrador',
                        'Publicado' => 'Publicado',
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

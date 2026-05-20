<?php

namespace App\Filament\Resources\Tours\Tables;

use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ToursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->recordClasses('!bg-transparent !shadow-none !ring-0 !p-0')
            ->columns([
                View::make('filament.resources.tours.card')
                    ->components([
                        TextColumn::make('nombre')
                            ->label('Nombre')
                            ->searchable()
                            ->extraAttributes(['class' => 'hidden']),

                        TextColumn::make('ubicacion')
                            ->label('Ubicación')
                            ->searchable()
                            ->extraAttributes(['class' => 'hidden']),

                        TextColumn::make('duracion')
                            ->label('Duración')
                            ->searchable()
                            ->extraAttributes(['class' => 'hidden']),

                        TextColumn::make('estado')
                            ->label('Estado')
                            ->searchable()
                            ->extraAttributes(['class' => 'hidden']),
                    ]),
            ])
            ->filters([
                TrashedFilter::make(),

                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'Disponible' => 'Disponible',
                        'No disponible' => 'No disponible',
                        'Archivado' => 'Archivado',
                    ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}

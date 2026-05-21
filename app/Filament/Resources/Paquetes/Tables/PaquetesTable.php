<?php

namespace App\Filament\Resources\Paquetes\Tables;

use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PaquetesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->recordClasses('!bg-transparent !shadow-none !ring-0 !p-0')
            ->recordAction(null)
            ->recordUrl(null)
            ->selectable(false)
            ->columns([
                View::make('filament.resources.paquetes.card')
                    ->components([
                        TextColumn::make('nombre')
                            ->label('Nombre')
                            ->searchable()
                            ->extraAttributes(['class' => 'hidden']),

                        TextColumn::make('tipo_habitacion')
                            ->label('Tipo habitación')
                            ->searchable()
                            ->extraAttributes(['class' => 'hidden']),

                        TextColumn::make('tour.nombre')
                            ->label('Tour')
                            ->searchable(['tour_incluido'])
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
                        'Borrador' => 'Borrador',
                        'Publicado' => 'Publicado',
                        'Archivado' => 'Archivado',
                    ]),

                SelectFilter::make('tipo_habitacion')
                    ->label('Tipo de habitación')
                    ->options([
                        'Simple' => 'Simple',
                        'Doble' => 'Doble',
                        'Matrimonial' => 'Matrimonial',
                        'Familiar' => 'Familiar',
                        'Suite' => 'Suite',
                    ]),

                TernaryFilter::make('incluye_desayuno')
                    ->label('Incluye desayuno')
                    ->native(false),

                TernaryFilter::make('incluye_almuerzo')
                    ->label('Incluye almuerzo')
                    ->native(false),

                TernaryFilter::make('incluye_cena')
                    ->label('Incluye cena')
                    ->native(false),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}

<?php

namespace App\Filament\Resources\Menus\Tables;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                            ->searchable()
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

                        TextColumn::make('observacion')
                            ->label('Observación')
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

                Filter::make('fecha_menu')
                    ->label('Fecha del menú')
                    ->form([
                        DatePicker::make('desde')
                            ->label('Desde'),
                        DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(filled($data['desde'] ?? null), fn (Builder $query): Builder => $query->whereDate('fecha_menu', '>=', $data['desde']))
                            ->when(filled($data['hasta'] ?? null), fn (Builder $query): Builder => $query->whereDate('fecha_menu', '<=', $data['hasta']));
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}

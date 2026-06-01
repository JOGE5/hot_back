<?php

namespace App\Filament\Resources\Paquetes\Tables;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                            ->searchable()
                            ->extraAttributes(['class' => 'hidden']),

                        TextColumn::make('tour_incluido')
                            ->label('Tour incluido')
                            ->searchable()
                            ->extraAttributes(['class' => 'hidden']),

                        TextColumn::make('estado')
                            ->label('Estado')
                            ->searchable()
                            ->extraAttributes(['class' => 'hidden']),

                        TextColumn::make('descripcion')
                            ->label('Descripción')
                            ->searchable()
                            ->extraAttributes(['class' => 'hidden']),

                        TextColumn::make('duracion_dias')
                            ->label('Duración')
                            ->searchable()
                            ->extraAttributes(['class' => 'hidden']),

                        TextColumn::make('precio_total')
                            ->label('Precio total')
                            ->searchable()
                            ->extraAttributes(['class' => 'hidden']),

                        TextColumn::make('observacion')
                            ->label('Observación')
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

                SelectFilter::make('tour_id')
                    ->label('Tour')
                    ->relationship('tour', 'nombre')
                    ->searchable()
                    ->preload()
                    ->native(false),

                TernaryFilter::make('incluye_desayuno')
                    ->label('Incluye desayuno')
                    ->native(false),

                TernaryFilter::make('incluye_almuerzo')
                    ->label('Incluye almuerzo')
                    ->native(false),

                TernaryFilter::make('incluye_cena')
                    ->label('Incluye cena')
                    ->native(false),

                Filter::make('duracion_dias')
                    ->label('Duracion en dias')
                    ->form(self::formularioRangoNumerico('Dias desde', 'Dias hasta'))
                    ->query(fn (Builder $query, array $data): Builder => self::aplicarRangoNumerico($query, 'duracion_dias', $data)),

                Filter::make('precio_total')
                    ->label('Precio total')
                    ->form(self::formularioRangoNumerico('Precio desde', 'Precio hasta'))
                    ->query(fn (Builder $query, array $data): Builder => self::aplicarRangoNumerico($query, 'precio_total', $data)),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    private static function formularioRangoNumerico(string $desde, string $hasta): array
    {
        return [
            TextInput::make('desde')
                ->label($desde)
                ->numeric()
                ->minValue(0),
            TextInput::make('hasta')
                ->label($hasta)
                ->numeric()
                ->minValue(0),
        ];
    }

    private static function aplicarRangoNumerico(Builder $query, string $campo, array $data): Builder
    {
        return $query
            ->when(filled($data['desde'] ?? null), fn (Builder $query): Builder => $query->where($campo, '>=', $data['desde']))
            ->when(filled($data['hasta'] ?? null), fn (Builder $query): Builder => $query->where($campo, '<=', $data['hasta']));
    }
}

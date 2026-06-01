<?php

namespace App\Filament\Resources\Tours\Tables;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

                        TextColumn::make('descripcion')
                            ->label('Descripción')
                            ->searchable()
                            ->extraAttributes(['class' => 'hidden']),

                        TextColumn::make('precio')
                            ->label('Precio')
                            ->searchable()
                            ->extraAttributes(['class' => 'hidden']),

                        TextColumn::make('cupos_disponibles')
                            ->label('Cupos disponibles')
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
                        'Disponible' => 'Disponible',
                        'No disponible' => 'No disponible',
                        'Archivado' => 'Archivado',
                    ]),

                SelectFilter::make('duracion')
                    ->label('Duración')
                    ->options(fn (): array => self::opcionesDistintas('duracion'))
                    ->searchable()
                    ->native(false),

                SelectFilter::make('ubicacion')
                    ->label('Ubicación')
                    ->options(fn (): array => self::opcionesDistintas('ubicacion'))
                    ->searchable()
                    ->native(false),

                Filter::make('precio')
                    ->label('Precio')
                    ->form(self::formularioRangoNumerico('Precio desde', 'Precio hasta'))
                    ->query(fn (Builder $query, array $data): Builder => self::aplicarRangoNumerico($query, 'precio', $data)),

                Filter::make('cupos_disponibles')
                    ->label('Cupos disponibles')
                    ->form(self::formularioRangoNumerico('Cupos desde', 'Cupos hasta'))
                    ->query(fn (Builder $query, array $data): Builder => self::aplicarRangoNumerico($query, 'cupos_disponibles', $data)),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    private static function opcionesDistintas(string $campo): array
    {
        return \App\Models\Tour::query()
            ->whereNotNull($campo)
            ->where($campo, '!=', '')
            ->distinct()
            ->orderBy($campo)
            ->pluck($campo, $campo)
            ->all();
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

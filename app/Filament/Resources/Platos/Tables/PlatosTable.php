<?php

namespace App\Filament\Resources\Platos\Tables;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

                        TextColumn::make('descripcion')
                            ->label('Descripción')
                            ->searchable(),

                        TextColumn::make('precio')
                            ->label('Precio')
                            ->searchable(),

                        TextColumn::make('tiempo_preparacion')
                            ->label('Tiempo de preparación')
                            ->searchable(),

                        TextColumn::make('observacion')
                            ->label('Observación')
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

                Filter::make('precio')
                    ->label('Precio')
                    ->form(self::formularioRangoNumerico('Precio desde', 'Precio hasta'))
                    ->query(fn (Builder $query, array $data): Builder => self::aplicarRangoNumerico($query, 'precio', $data)),

                Filter::make('tiempo_preparacion')
                    ->label('Tiempo de preparación')
                    ->form(self::formularioRangoNumerico('Minutos desde', 'Minutos hasta'))
                    ->query(fn (Builder $query, array $data): Builder => self::aplicarRangoNumerico($query, 'tiempo_preparacion', $data)),
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

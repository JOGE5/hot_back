<?php

namespace App\Filament\Resources\Consumos\Tables;

use App\Models\Consumo;
use App\Models\Habitacion;
use App\Models\Huesped;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConsumosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('fecha_consumo')
                    ->label('Fecha de consumo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('plato.nombre')
                    ->label('Plato')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (?string $state): string => $state ?? 'Plato no disponible'),

                TextColumn::make('huesped.nombres')
                    ->label('Huésped')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (?string $state, Consumo $record): string {
                        if (! $record->huesped) {
                            return 'Huésped no disponible';
                        }

                        return trim(
                            ($record->huesped->nombres ?? '') . ' ' .
                            ($record->huesped->apellido_paterno ?? '') . ' ' .
                            ($record->huesped->apellido_materno ?? '')
                        );
                    }),

                TextColumn::make('habitacion.numero')
                    ->label('Habitación')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (?string $state): string => $state ?? 'Habitación no disponible'),

                TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->sortable(),

                TextColumn::make('precio_unitario')
                    ->label('Precio unitario')
                    ->money('BOB')
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('BOB')
                    ->sortable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->sortable()
                    ->badge()
                    ->colors([
                        'success' => 'Confirmado',
                        'warning' => 'Pendiente',
                        'danger' => 'Anulado',
                    ]),

                TextColumn::make('registradoPor.nombres')
                    ->label('Registrado por')
                    ->toggleable()
                    ->sortable()
                    ->formatStateUsing(function (?string $state, Consumo $record): string {
                        if (! $record->registradoPor) {
                            return 'No disponible';
                        }

                        return trim(
                            ($record->registradoPor->nombres ?? $record->registradoPor->name ?? '') . ' ' .
                            ($record->registradoPor->apellido_paterno ?? '')
                        );
                    }),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('plato_id')
                    ->label('Plato')
                    ->relationship('plato', 'nombre')
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('huesped_id')
                    ->label('Huésped')
                    ->relationship('huesped', 'nombres')
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('habitacion_id')
                    ->label('Habitación')
                    ->relationship('habitacion', 'numero')
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Confirmado' => 'Confirmado',
                        'Anulado' => 'Anulado',
                    ]),

                Filter::make('fecha_consumo')
                    ->label('Fecha de consumo')
                    ->form([
                        DateTimePicker::make('desde')
                            ->label('Desde')
                            ->withoutSeconds()
                            ->displayFormat('d/m/Y H:i'),

                        DateTimePicker::make('hasta')
                            ->label('Hasta')
                            ->withoutSeconds()
                            ->displayFormat('d/m/Y H:i'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                filled($data['desde'] ?? null),
                                fn (Builder $query) => $query->where('fecha_consumo', '>=', $data['desde']),
                            )
                            ->when(
                                filled($data['hasta'] ?? null),
                                fn (Builder $query) => $query->where('fecha_consumo', '<=', $data['hasta']),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver'),

                EditAction::make()
                    ->label('Editar'),
            ])
            ->toolbarActions([]);
    }
}

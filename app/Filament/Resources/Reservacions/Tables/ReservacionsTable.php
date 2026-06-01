<?php

namespace App\Filament\Resources\Reservacions\Tables;

use App\Filament\Resources\Reservacions\ReservacionResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha_salida')
                    ->date()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cantidad_personas')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('estado_reservacion')
                    ->searchable(),
                TextColumn::make('metodo_pago')
                    ->searchable(),
                TextColumn::make('estado_pago')
                    ->searchable(),
                TextColumn::make('codigo_checkin')
                    ->searchable(),
                TextColumn::make('huesped.correo_electronico')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('huesped.telefono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('observacion')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('estado_reservacion')
                    ->label('Estado de reservación')
                    ->options([
                        'Pendiente de pago' => 'Pendiente de pago',
                        'Confirmada' => 'Confirmada',
                        'En estadía' => 'En estadía',
                        'Cancelada' => 'Cancelada',
                        'Finalizada' => 'Finalizada',
                    ]),
                SelectFilter::make('estado_pago')
                    ->label('Estado de pago')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Confirmado' => 'Confirmado',
                        'Rechazado' => 'Rechazado',
                    ]),
                SelectFilter::make('origen_reservacion')
                    ->label('Origen')
                    ->options([
                        'En línea' => 'En línea',
                        'Recepción presencial' => 'Recepción presencial',
                    ]),
                SelectFilter::make('metodo_pago')
                    ->label('Método de pago')
                    ->options([
                        'Efectivo' => 'Efectivo',
                        'Tarjeta' => 'Tarjeta',
                        'Transferencia' => 'Transferencia',
                        'QR' => 'QR',
                    ]),
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
                Filter::make('fecha_entrada')
                    ->label('Fecha de entrada')
                    ->form(self::formularioRangoFecha())
                    ->query(fn (Builder $query, array $data): Builder => self::aplicarRangoFecha($query, 'fecha_entrada', $data)),
                Filter::make('fecha_salida')
                    ->label('Fecha de salida')
                    ->form(self::formularioRangoFecha())
                    ->query(fn (Builder $query, array $data): Builder => self::aplicarRangoFecha($query, 'fecha_salida', $data)),
                Filter::make('total')
                    ->label('Total')
                    ->form(self::formularioRangoNumerico('Total desde', 'Total hasta'))
                    ->query(fn (Builder $query, array $data): Builder => self::aplicarRangoNumerico($query, 'total', $data)),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn ($record): bool => ReservacionResource::canEdit($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function formularioRangoFecha(): array
    {
        return [
            DatePicker::make('desde')->label('Desde'),
            DatePicker::make('hasta')->label('Hasta'),
        ];
    }

    private static function formularioRangoNumerico(string $desde, string $hasta): array
    {
        return [
            TextInput::make('desde')->label($desde)->numeric()->minValue(0),
            TextInput::make('hasta')->label($hasta)->numeric()->minValue(0),
        ];
    }

    private static function aplicarRangoFecha(Builder $query, string $campo, array $data): Builder
    {
        return $query
            ->when(filled($data['desde'] ?? null), fn (Builder $query): Builder => $query->whereDate($campo, '>=', $data['desde']))
            ->when(filled($data['hasta'] ?? null), fn (Builder $query): Builder => $query->whereDate($campo, '<=', $data['hasta']));
    }

    private static function aplicarRangoNumerico(Builder $query, string $campo, array $data): Builder
    {
        return $query
            ->when(filled($data['desde'] ?? null), fn (Builder $query): Builder => $query->where($campo, '>=', $data['desde']))
            ->when(filled($data['hasta'] ?? null), fn (Builder $query): Builder => $query->where($campo, '<=', $data['hasta']));
    }
}

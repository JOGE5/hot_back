<?php

namespace App\Filament\Resources\Ingredientes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

class IngredientesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('unidad_medida')
                    ->searchable()
                    ->badge(),
                TextColumn::make('stock_actual')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stock_minimo')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('costo_unitario')
                    ->numeric()
                    ->sortable()
                    ->money('USD'), // Assuming standard format, we can just use numeric()
                TextColumn::make('fecha_vencimiento')
                    ->date()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('estado_visual')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($record) => match ($record->estado_visual) {
                        'Disponible' => 'success',
                        'Bajo stock' => 'warning',
                        'Agotado' => 'danger',
                        'Vencido' => 'gray',
                        default => 'primary',
                    }),
                TextColumn::make('proveedor')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('observacion')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('unidad_medida')
                    ->options([
                        'kg' => 'Kilogramos',
                        'g' => 'Gramos',
                        'l' => 'Litros',
                        'ml' => 'Mililitros',
                        'unidad' => 'Unidad',
                        'paquete' => 'Paquete',
                    ]),
                SelectFilter::make('proveedor')
                    ->label('Proveedor')
                    ->options(fn (): array => self::opcionesDistintas('proveedor'))
                    ->searchable()
                    ->native(false),
                Filter::make('estado_visual')
                    ->form([
                        Select::make('estado')
                            ->options([
                                'Disponible' => 'Disponible',
                                'Bajo stock' => 'Bajo stock',
                                'Agotado' => 'Agotado',
                                'Vencido' => 'Vencido',
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['estado'],
                            fn (Builder $query, $estado) => match($estado) {
                                'Vencido' => $query->whereNotNull('fecha_vencimiento')->where('fecha_vencimiento', '<', now()),
                                'Agotado' => $query->where('stock_actual', 0),
                                'Bajo stock' => $query->where('stock_actual', '>', 0)->whereColumn('stock_actual', '<=', 'stock_minimo'),
                                'Disponible' => $query->whereColumn('stock_actual', '>', 'stock_minimo')
                                    ->where(function($q) {
                                        $q->whereNull('fecha_vencimiento')->orWhere('fecha_vencimiento', '>=', now());
                                    }),
                                default => $query,
                            }
                        );
                    }),
                Filter::make('fecha_vencimiento')
                    ->label('Fecha de vencimiento')
                    ->form([
                        DatePicker::make('desde')
                            ->label('Desde'),
                        DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => self::aplicarRangoFecha($query, 'fecha_vencimiento', $data)),
                Filter::make('stock_actual')
                    ->label('Stock actual')
                    ->form(self::formularioRangoNumerico('Stock desde', 'Stock hasta'))
                    ->query(fn (Builder $query, array $data): Builder => self::aplicarRangoNumerico($query, 'stock_actual', $data)),
                Filter::make('stock_minimo')
                    ->label('Stock mínimo')
                    ->form(self::formularioRangoNumerico('Mínimo desde', 'Mínimo hasta'))
                    ->query(fn (Builder $query, array $data): Builder => self::aplicarRangoNumerico($query, 'stock_minimo', $data)),
                Filter::make('costo_unitario')
                    ->label('Costo unitario')
                    ->form(self::formularioRangoNumerico('Costo desde', 'Costo hasta'))
                    ->query(fn (Builder $query, array $data): Builder => self::aplicarRangoNumerico($query, 'costo_unitario', $data)),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    private static function opcionesDistintas(string $campo): array
    {
        return \App\Models\Ingrediente::query()
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

    private static function aplicarRangoFecha(Builder $query, string $campo, array $data): Builder
    {
        return $query
            ->when(filled($data['desde'] ?? null), fn (Builder $query): Builder => $query->whereDate($campo, '>=', $data['desde']))
            ->when(filled($data['hasta'] ?? null), fn (Builder $query): Builder => $query->whereDate($campo, '<=', $data['hasta']));
    }
}

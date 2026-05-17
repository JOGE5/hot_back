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
                    })
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
}

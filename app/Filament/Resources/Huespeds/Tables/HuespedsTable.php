<?php

namespace App\Filament\Resources\Huespeds\Tables;

use App\Filament\Resources\Huespeds\HuespedResource;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HuespedsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombres')
                    ->label('Nombres')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('apellido_paterno')
                    ->label('Apellido paterno')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('apellido_materno')
                    ->label('Apellido materno')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('numero_documento')
                    ->label('Documento')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),

                TextColumn::make('correo_electronico')
                    ->label('Correo electrónico')
                    ->searchable(),

                IconColumn::make('estado')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('estado_reserva_visual')
                    ->label('Estado reserva')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'En reservación' => 'warning',
                        'Sin reserva activa' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->searchable([
                fn (Builder $query, string $search): Builder => self::aplicarBusquedaAvanzada($query, $search),
            ])
            ->filters([
                TernaryFilter::make('estado')
                    ->label('Estado')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos')
                    ->native(false),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver'),

                EditAction::make()
                    ->label('Editar')
                    ->visible(fn ($record): bool => HuespedResource::canEdit($record)),
            ])
            ->toolbarActions([]);
    }

    private static function aplicarBusquedaAvanzada(Builder $query, string $search): Builder
    {
        $search = trim($search);
        $like = '%' . mb_strtolower($search) . '%';
        $estado = self::estadoDesdeBusqueda($search);

        return $query
            ->whereRaw('LOWER(nombres) LIKE ?', [$like])
            ->orWhereRaw('LOWER(apellido_paterno) LIKE ?', [$like])
            ->orWhereRaw('LOWER(apellido_materno) LIKE ?', [$like])
            ->orWhereRaw("LOWER(CONCAT_WS(' ', nombres, apellido_paterno, apellido_materno)) LIKE ?", [$like])
            ->orWhereRaw('LOWER(numero_documento) LIKE ?', [$like])
            ->orWhereRaw('LOWER(telefono) LIKE ?', [$like])
            ->orWhereRaw('LOWER(correo_electronico) LIKE ?', [$like])
            ->orWhereRaw('LOWER(nacionalidad) LIKE ?', [$like])
            ->orWhereRaw("DATE_FORMAT(fecha_nacimiento, '%Y-%m-%d') LIKE ?", [$like])
            ->orWhereRaw("DATE_FORMAT(fecha_nacimiento, '%d/%m/%Y') LIKE ?", [$like])
            ->when(
                $estado !== null,
                fn (Builder $query): Builder => $query->orWhere('estado', $estado)
            );
    }

    private static function estadoDesdeBusqueda(string $search): ?bool
    {
        $search = mb_strtolower(trim($search));

        return match ($search) {
            'activo', 'activa', '1', 'si' => true,
            'inactivo', 'inactiva', '0', 'no' => false,
            default => null,
        };
    }
}

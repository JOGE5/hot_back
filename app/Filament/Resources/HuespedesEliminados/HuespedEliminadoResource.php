<?php

namespace App\Filament\Resources\HuespedesEliminados;

use App\Filament\Resources\HuespedesEliminados\Pages\ListHuespedesEliminados;
use App\Models\Huesped;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HuespedEliminadoResource extends Resource
{
    protected static ?string $model = Huesped::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'papelera-huespedes';

    protected static ?string $modelLabel = 'Papelera de huéspedes';

    protected static ?string $pluralModelLabel = 'Papelera de huéspedes';

    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();
        return $user?->role && in_array($user->role->nombre, ['SUPER ADMIN', 'ADMIN'], true);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canForceDelete(Model $record): bool
    {
        return false;
    }

    public static function canForceDeleteAny(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
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

                TextColumn::make('tipo_documento')
                    ->label('Tipo de documento')
                    ->sortable(),

                TextColumn::make('numero_documento')
                    ->label('Número de documento')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),

                TextColumn::make('correo_electronico')
                    ->label('Correo electrónico')
                    ->searchable(),

                TextColumn::make('nacionalidad')
                    ->label('Nacionalidad')
                    ->searchable(),

                TextColumn::make('fecha_nacimiento')
                    ->label('Fecha de nacimiento')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('estado_baja')
                    ->label('Estado')
                    ->state(fn (): string => 'Inactivo')
                    ->badge()
                    ->color('danger'),

                TextColumn::make('deleted_at')
                    ->label('Dado de baja en')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('papelera_vaciada_at')
                    ->label('Papelera vaciada en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo_documento')
                    ->label('Tipo de documento')
                    ->options(fn (): array => self::opcionesDistintas('tipo_documento'))
                    ->native(false),

                SelectFilter::make('nacionalidad')
                    ->label('Nacionalidad')
                    ->options(fn (): array => self::opcionesDistintas('nacionalidad'))
                    ->searchable()
                    ->native(false),

                Filter::make('deleted_at')
                    ->label('Fecha de baja')
                    ->form(self::formularioRangoFecha())
                    ->query(fn (Builder $query, array $data): Builder => self::aplicarRangoFecha($query, 'deleted_at', $data)),

                Filter::make('papelera_vaciada_at')
                    ->label('Papelera vaciada en')
                    ->form(self::formularioRangoFecha())
                    ->query(fn (Builder $query, array $data): Builder => self::aplicarRangoFecha($query, 'papelera_vaciada_at', $data)),
            ])
            ->recordActions([
                Action::make('recuperar_huesped')
                    ->label('Recuperar huésped')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Recuperar huésped')
                    ->modalDescription('El huésped volverá a estar activo en la lista principal.')
                    ->modalSubmitActionLabel('Sí, recuperar')
                    ->action(function (Huesped $record): void {
                        $record->restore();

                        Notification::make()
                            ->title('Huésped recuperado correctamente')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        $query = Huesped::query()
            ->onlyTrashed()
            ->whereNull('papelera_vaciada_at');

        if (! $user?->role) {
            return $query->whereRaw('1 = 0');
        }

        if (in_array($user->role->nombre, ['SUPER ADMIN', 'ADMIN'], true)) {
            return $query;
        }

        return $query->whereRaw('1 = 0');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHuespedesEliminados::route('/'),
        ];
    }

    private static function opcionesDistintas(string $campo): array
    {
        return Huesped::onlyTrashed()
            ->whereNull('papelera_vaciada_at')
            ->whereNotNull($campo)
            ->where($campo, '!=', '')
            ->distinct()
            ->orderBy($campo)
            ->pluck($campo, $campo)
            ->all();
    }

    private static function formularioRangoFecha(): array
    {
        return [
            DatePicker::make('desde')
                ->label('Desde'),
            DatePicker::make('hasta')
                ->label('Hasta'),
        ];
    }

    private static function aplicarRangoFecha(Builder $query, string $campo, array $data): Builder
    {
        return $query
            ->when(filled($data['desde'] ?? null), fn (Builder $query): Builder => $query->whereDate($campo, '>=', $data['desde']))
            ->when(filled($data['hasta'] ?? null), fn (Builder $query): Builder => $query->whereDate($campo, '<=', $data['hasta']));
    }
}

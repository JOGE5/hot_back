<?php

namespace App\Filament\Resources\LogUsers;

use App\Filament\Resources\LogUsers\Pages\ListLogUsers;
use App\Models\LogUser;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class LogUserResource extends Resource
{
    protected static ?string $model = LogUser::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'Seguridad';

    protected static ?string $navigationLabel = 'Bitácora';

    protected static ?string $modelLabel = 'bitácora';

    protected static ?string $pluralModelLabel = 'bitácora';

    protected static ?string $slug = 'bitacora';

    public static function canViewAny(): bool
    {
        return self::tieneRol(['SUPER ADMIN', 'ADMIN']);
    }

    public static function canView(Model $record): bool
    {
        return self::canViewAny();
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

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->placeholder('Sistema')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rol')
                    ->label('Rol')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('accion')
                    ->label('Acción')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('modulo')
                    ->label('Módulo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable()
                    ->limit(80)
                    ->wrap(),

                TextColumn::make('ip')
                    ->label('IP')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('rol')
                    ->label('Rol')
                    ->options(fn (): array => LogUser::query()
                        ->whereNotNull('rol')
                        ->distinct()
                        ->orderBy('rol')
                        ->pluck('rol', 'rol')
                        ->all()),

                SelectFilter::make('modulo')
                    ->label('Módulo')
                    ->options(fn (): array => LogUser::query()
                        ->whereNotNull('modulo')
                        ->distinct()
                        ->orderBy('modulo')
                        ->pluck('modulo', 'modulo')
                        ->all()),

                SelectFilter::make('accion')
                    ->label('Acción')
                    ->options(fn (): array => LogUser::query()
                        ->whereNotNull('accion')
                        ->distinct()
                        ->orderBy('accion')
                        ->pluck('accion', 'accion')
                        ->all()),

                Filter::make('created_at')
                    ->label('Fecha')
                    ->form([
                        DatePicker::make('desde')
                            ->label('Desde'),

                        DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                filled($data['desde'] ?? null),
                                fn (Builder $query) => $query->whereDate('created_at', '>=', $data['desde']),
                            )
                            ->when(
                                filled($data['hasta'] ?? null),
                                fn (Builder $query) => $query->whereDate('created_at', '<=', $data['hasta']),
                            );
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLogUsers::route('/'),
        ];
    }

    private static function tieneRol(array $roles): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, $roles, true);
    }
}

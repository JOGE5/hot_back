<?php

namespace App\Filament\Resources\UsuariosEliminados;

use App\Filament\Resources\UsuariosEliminados\Pages\ListUsuariosEliminados;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class UsuarioEliminadoResource extends Resource
{
    protected static ?string $model = User::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-trash';

    protected static string|UnitEnum|null $navigationGroup = 'Seguridad';

    protected static ?string $navigationLabel = 'Papelera de usuarios';

    protected static ?string $modelLabel = 'usuario dado de baja';

    protected static ?string $pluralModelLabel = 'usuarios dados de baja';

    protected static ?string $slug = 'papelera-usuarios';

    protected static ?string $recordTitleAttribute = 'name';

    public const ROLES_ADMINISTRATIVOS = [
        'SUPER ADMIN',
        'ADMIN',
        'RECEPCIONISTA',
        'CHEF',
    ];

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

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Correo electrónico')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role.nombre')
                    ->label('Rol')
                    ->badge()
                    ->searchable()
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

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('recuperar_usuario')
                    ->label('Recuperar usuario')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Recuperar usuario administrativo')
                    ->modalDescription('El usuario volverá a aparecer en Usuarios administrativos.')
                    ->modalSubmitActionLabel('Sí, recuperar')
                    ->visible(fn ($record): bool => self::puedeRecuperarUsuario($record))
                    ->action(function (User $record): void {
                        $record->restore();

                        Notification::make()
                            ->title('Usuario recuperado correctamente')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        $query = User::query()
            ->onlyTrashed()
            ->whereHas('role', function (Builder $query) {
                $query->whereIn('nombre', self::ROLES_ADMINISTRATIVOS);
            });

        if (! $user?->role) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->role->nombre === 'SUPER ADMIN') {
            return $query;
        }

        if ($user->role->nombre === 'ADMIN') {
            return $query->whereHas('role', function (Builder $query) {
                $query->whereIn('nombre', ['RECEPCIONISTA', 'CHEF']);
            });
        }

        return $query->whereRaw('1 = 0');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsuariosEliminados::route('/'),
        ];
    }

    public static function puedeRecuperarUsuario(Model $record): bool
    {
        $user = Filament::auth()->user();

        if (! $user?->role || ! $record->role) {
            return false;
        }

        if (! in_array($record->role->nombre, self::ROLES_ADMINISTRATIVOS, true)) {
            return false;
        }

        if ($user->role->nombre === 'SUPER ADMIN') {
            return true;
        }

        if ($user->role->nombre === 'ADMIN') {
            return in_array($record->role->nombre, ['RECEPCIONISTA', 'CHEF'], true);
        }

        return false;
    }
}
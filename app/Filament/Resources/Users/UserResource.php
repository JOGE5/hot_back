<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Seguridad';

    protected static ?string $navigationLabel = 'Usuarios administrativos';

    protected static ?string $modelLabel = 'usuario administrativo';

    protected static ?string $pluralModelLabel = 'usuarios administrativos';

    protected static ?string $recordTitleAttribute = 'name';

    public const ROLES_ADMINISTRATIVOS = [
        'SUPER ADMIN',
        'ADMIN',
        'RECEPCIONISTA',
        'CHEF',
    ];

    public const ROLES_CREABLES_SUPER_ADMIN = [
        'ADMIN',
        'RECEPCIONISTA',
        'CHEF',
    ];

    public const ROLES_CREABLES_ADMIN = [
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
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, ['SUPER ADMIN', 'ADMIN'], true);
    }

    public static function canView(Model $record): bool
    {
        return self::puedeGestionarUsuario($record, permitirPropio: true);
    }

    public static function canEdit(Model $record): bool
    {
        return self::puedeGestionarUsuario($record, permitirPropio: false);
    }

    public static function canDelete(Model $record): bool
    {
        return self::puedeGestionarUsuario($record, permitirPropio: false);
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
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        $query = parent::getEloquentQuery()
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function puedeGestionarUsuario(Model $record, bool $permitirPropio = true): bool
    {
        $user = Filament::auth()->user();

        if (! $user?->role || ! $record->role) {
            return false;
        }

        if (! $permitirPropio && (int) $user->id === (int) $record->id) {
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
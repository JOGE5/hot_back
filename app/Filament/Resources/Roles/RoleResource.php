<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\Pages\ViewRole;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Filament\Resources\Roles\Schemas\RoleInfolist;
use App\Filament\Resources\Roles\Tables\RolesTable;
use App\Models\Role;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|UnitEnum|null $navigationGroup = 'Seguridad';

    protected static ?string $navigationLabel = 'Roles administrativos';

    protected static ?string $modelLabel = 'rol administrativo';

    protected static ?string $pluralModelLabel = 'roles administrativos';

    protected static ?string $recordTitleAttribute = 'nombre';

    public const ROLES_ADMINISTRATIVOS = [
        'SUPER ADMIN',
        'ADMIN',
        'RECEPCIONISTA',
        'CHEF',
    ];

    public const ROLES_BASE = [
        'SUPER ADMIN',
        'ADMIN',
        'RECEPCIONISTA',
        'CHEF',
        'HUESPED',
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
        $user = Filament::auth()->user();
         if (! $user?->role) {
        return false;
     }

    // Solo se pueden editar roles administrativos visibles.
    if (! in_array($record->nombre, self::ROLES_ADMINISTRATIVOS, true)) {
        return false;
     }

    // SUPER ADMIN puede editar roles administrativos.
    if ($user->role->nombre === 'SUPER ADMIN') {
        return true;
     }

    // ADMIN no puede editar SUPER ADMIN.
    if ($user->role->nombre === 'ADMIN') {
        return $record->nombre !== 'SUPER ADMIN';
     }


        return false;

    }
    public static function canView(Model $record): bool
    {
    return in_array($record->nombre, self::ROLES_ADMINISTRATIVOS, true);
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
        return RoleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RoleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->whereIn('nombre', self::ROLES_ADMINISTRATIVOS);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'view' => ViewRole::route('/{record}'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
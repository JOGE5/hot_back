<?php

namespace App\Filament\Resources\Huespeds;

use App\Filament\Resources\Huespeds\Pages\CreateHuesped;
use App\Filament\Resources\Huespeds\Pages\EditHuesped;
use App\Filament\Resources\Huespeds\Pages\ListHuespeds;
use App\Filament\Resources\Huespeds\Pages\ViewHuesped;
use App\Filament\Resources\Huespeds\Schemas\HuespedForm;
use App\Filament\Resources\Huespeds\Schemas\HuespedInfolist;
use App\Filament\Resources\Huespeds\Tables\HuespedsTable;
use App\Models\Huesped;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class HuespedResource extends Resource
{
    protected static ?string $model = Huesped::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Hotel';

    protected static ?string $navigationLabel = 'Huéspedes';

    protected static ?string $modelLabel = 'huésped';

    protected static ?string $pluralModelLabel = 'huéspedes';

    protected static ?string $recordTitleAttribute = 'nombres';

    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, [
            'SUPER ADMIN',
            'ADMIN',
            'RECEPCIONISTA',
        ], true);
    }

    public static function canCreate(): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, [
            'SUPER ADMIN',
            'ADMIN',
        ], true);
    }

    public static function canView(Model $record): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, [
            'SUPER ADMIN',
            'ADMIN',
            'RECEPCIONISTA',
        ], true);
    }

    public static function canEdit(Model $record): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, [
            'SUPER ADMIN',
            'ADMIN',
        ], true);
    }

    public static function canDelete(Model $record): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, [
            'SUPER ADMIN',
            'ADMIN',
        ], true);
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
        return HuespedForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HuespedInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HuespedsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHuespeds::route('/'),
            'create' => CreateHuesped::route('/create'),
            'view' => ViewHuesped::route('/{record}'),
            'edit' => EditHuesped::route('/{record}/edit'),
        ];
    }
}
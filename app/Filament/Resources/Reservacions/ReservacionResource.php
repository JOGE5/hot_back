<?php

namespace App\Filament\Resources\Reservacions;

use App\Filament\Resources\Reservacions\Pages\CreateReservacion;
use App\Filament\Resources\Reservacions\Pages\EditReservacion;
use App\Filament\Resources\Reservacions\Pages\ListReservacions;
use App\Filament\Resources\Reservacions\Pages\ViewReservacion;
use App\Filament\Resources\Reservacions\Schemas\ReservacionForm;
use App\Filament\Resources\Reservacions\Schemas\ReservacionInfolist;
use App\Filament\Resources\Reservacions\Tables\ReservacionsTable;
use App\Models\Reservacion;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ReservacionResource extends Resource
{
    protected static ?string $model = Reservacion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Hotel';

    protected static ?string $navigationLabel = 'Reservaciones';

    protected static ?string $modelLabel = 'reservación';

    protected static ?string $pluralModelLabel = 'reservaciones';

    protected static ?string $recordTitleAttribute = 'codigo_checkin';

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
            'RECEPCIONISTA',
        ], true);
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, [
            'SUPER ADMIN',
            'ADMIN',
            'RECEPCIONISTA',
        ], true);
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
        return ReservacionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ReservacionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReservacionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReservacions::route('/'),
            'create' => CreateReservacion::route('/create'),
            'view' => ViewReservacion::route('/{record}'),
            'edit' => EditReservacion::route('/{record}/edit'),
        ];
    }
}
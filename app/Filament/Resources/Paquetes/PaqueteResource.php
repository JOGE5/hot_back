<?php

namespace App\Filament\Resources\Paquetes;

use App\Filament\Resources\Paquetes\Pages\CreatePaquete;
use App\Filament\Resources\Paquetes\Pages\EditPaquete;
use App\Filament\Resources\Paquetes\Pages\ListPaquetes;
use App\Filament\Resources\Paquetes\Schemas\PaqueteForm;
use App\Filament\Resources\Paquetes\Tables\PaquetesTable;
use App\Models\Paquete;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PaqueteResource extends Resource
{
    protected static ?string $model = Paquete::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Experiencias';

    protected static ?string $navigationLabel = 'Paquetes';

    protected static ?string $modelLabel = 'paquete';

    protected static ?string $pluralModelLabel = 'paquetes';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function canViewAny(): bool
    {
        return self::tieneRol(['SUPER ADMIN', 'ADMIN', 'RECEPCIONISTA']);
    }

    public static function canView(Model $record): bool
    {
        return self::canViewAny();
    }

    public static function canCreate(): bool
    {
        return self::tieneRol(['SUPER ADMIN', 'ADMIN']);
    }

    public static function canEdit(Model $record): bool
    {
        return self::tieneRol(['SUPER ADMIN', 'ADMIN']);
    }

    public static function canDelete(Model $record): bool
    {
        return self::tieneRol(['SUPER ADMIN', 'ADMIN']);
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

    public static function canRestore(Model $record): bool
    {
        return self::tieneRol(['SUPER ADMIN', 'ADMIN']);
    }

    public static function canRestoreAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return PaqueteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaquetesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaquetes::route('/'),
            'create' => CreatePaquete::route('/create'),
            'edit' => EditPaquete::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->with('tour')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('tour');
    }

    private static function tieneRol(array $roles): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, $roles, true);
    }
}

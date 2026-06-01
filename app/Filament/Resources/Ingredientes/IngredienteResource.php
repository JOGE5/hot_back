<?php

namespace App\Filament\Resources\Ingredientes;

use App\Filament\Resources\Ingredientes\Pages\CreateIngrediente;
use App\Filament\Resources\Ingredientes\Pages\EditIngrediente;
use App\Filament\Resources\Ingredientes\Pages\ListIngredientes;
use App\Filament\Resources\Ingredientes\Schemas\IngredienteForm;
use App\Filament\Resources\Ingredientes\Tables\IngredientesTable;
use App\Models\Ingrediente;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class IngredienteResource extends Resource
{
    protected static ?string $model = Ingrediente::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Gastronomía';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Inventario de ingredientes';

    protected static ?string $modelLabel = 'ingrediente';

    protected static ?string $pluralModelLabel = 'ingredientes';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function canViewAny(): bool
    {
        return self::tieneRol(['SUPER ADMIN', 'ADMIN', 'CHEF']);
    }

    public static function canView(Model $record): bool
    {
        return self::canViewAny();
    }

    public static function canCreate(): bool
    {
        return self::tieneRol(['SUPER ADMIN', 'ADMIN', 'CHEF']);
    }

    public static function canEdit(Model $record): bool
    {
        return self::tieneRol(['SUPER ADMIN', 'ADMIN', 'CHEF']);
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

    public static function form(Schema $schema): Schema
    {
        return IngredienteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IngredientesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIngredientes::route('/'),
            'create' => CreateIngrediente::route('/create'),
            'edit' => EditIngrediente::route('/{record}/edit'),
        ];
    }

    private static function tieneRol(array $roles): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, $roles, true);
    }
}

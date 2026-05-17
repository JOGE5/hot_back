<?php

namespace App\Filament\Resources\Ingredientes;

use App\Filament\Resources\Ingredientes\Pages\CreateIngrediente;
use App\Filament\Resources\Ingredientes\Pages\EditIngrediente;
use App\Filament\Resources\Ingredientes\Pages\ListIngredientes;
use App\Filament\Resources\Ingredientes\Schemas\IngredienteForm;
use App\Filament\Resources\Ingredientes\Tables\IngredientesTable;
use App\Models\Ingrediente;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class IngredienteResource extends Resource
{
    protected static ?string $model = Ingrediente::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Gastronomía';

    protected static ?string $navigationLabel = 'Inventario de ingredientes';

    protected static ?string $modelLabel = 'ingrediente';

    protected static ?string $pluralModelLabel = 'ingredientes';

    protected static ?string $recordTitleAttribute = 'nombre';

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
}
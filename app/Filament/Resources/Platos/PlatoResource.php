<?php

namespace App\Filament\Resources\Platos;

use App\Filament\Resources\Platos\Pages\CreatePlato;
use App\Filament\Resources\Platos\Pages\EditPlato;
use App\Filament\Resources\Platos\Pages\ListPlatos;
use App\Filament\Resources\Platos\Schemas\PlatoForm;
use App\Filament\Resources\Platos\Tables\PlatosTable;
use App\Models\Plato;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PlatoResource extends Resource
{
    protected static ?string $model = Plato::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Gastronomía';

    protected static ?string $modelLabel = 'plato';

    protected static ?string $pluralModelLabel = 'platos';

    protected static ?string $navigationLabel = 'Platos';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return PlatoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlatosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlatos::route('/'),
            'create' => CreatePlato::route('/create'),
            'edit' => EditPlato::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('ingredientes');
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
<?php

namespace App\Filament\Resources\Consumos;

use App\Filament\Resources\Consumos\Pages\CreateConsumo;
use App\Filament\Resources\Consumos\Pages\EditConsumo;
use App\Filament\Resources\Consumos\Pages\ListConsumos;
use App\Filament\Resources\Consumos\Pages\PlatosPopulares;
use App\Filament\Resources\Consumos\Pages\ReporteConsumos;
use App\Filament\Resources\Consumos\Pages\ViewConsumo;
use App\Filament\Resources\Consumos\Tables\ConsumosTable;
use App\Filament\Resources\Consumos\Schemas\ConsumoForm;
use App\Models\Consumo;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ConsumoResource extends Resource
{
    protected static ?string $model = Consumo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static string|UnitEnum|null $navigationGroup = 'Gastronomía';

    protected static ?string $navigationLabel = 'Consumos';

    protected static ?string $modelLabel = 'consumo';

    protected static ?string $pluralModelLabel = 'consumos';

    protected static ?string $recordTitleAttribute = 'id';

    public static function canViewAny(): bool
    {
        return self::tieneRol(['SUPER ADMIN', 'ADMIN', 'RECEPCIONISTA', 'CHEF']);
    }

    public static function canView(Model $record): bool
    {
        return self::canViewAny();
    }

    public static function canCreate(): bool
    {
        return self::tieneRol(['SUPER ADMIN', 'ADMIN', 'RECEPCIONISTA']);
    }

    public static function canEdit(Model $record): bool
    {
        return self::tieneRol(['SUPER ADMIN', 'ADMIN', 'RECEPCIONISTA']);
    }

    public static function canDelete(Model $record): bool
    {
        return self::tieneRol(['SUPER ADMIN', 'ADMIN']);
    }

    public static function canDeleteAny(): bool
    {
        return self::canDelete(new Consumo());
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
        return ConsumoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConsumosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConsumos::route('/'),
            'create' => CreateConsumo::route('/create'),
            'platos-populares' => PlatosPopulares::route('/platos-populares'),
            'reporte' => ReporteConsumos::route('/reporte'),
            'view' => ViewConsumo::route('/{record}'),
            'edit' => EditConsumo::route('/{record}/edit'),
        ];
    }

    private static function tieneRol(array $roles): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, $roles, true);
    }
}

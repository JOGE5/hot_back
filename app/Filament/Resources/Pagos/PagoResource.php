<?php

namespace App\Filament\Resources\Pagos;

use App\Filament\Resources\Pagos\Pages\CreatePago;
use App\Filament\Resources\Pagos\Pages\EditPago;
use App\Filament\Resources\Pagos\Pages\ListPagos;
use App\Filament\Resources\Pagos\Pages\ViewPago;
use App\Filament\Resources\Pagos\Schemas\PagoForm;
use App\Filament\Resources\Pagos\Schemas\PagoInfolist;
use App\Filament\Resources\Pagos\Tables\PagosTable;
use App\Models\Pago;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static string|UnitEnum|null $navigationGroup = 'Hotel';

    protected static ?string $navigationLabel = 'Pagos';

    protected static ?string $modelLabel = 'pago';

    protected static ?string $pluralModelLabel = 'pagos';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return PagoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PagoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PagosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPagos::route('/'),
            'create' => CreatePago::route('/create'),
            'view' => ViewPago::route('/{record}'),
            'edit' => EditPago::route('/{record}/edit'),
        ];
    }
}
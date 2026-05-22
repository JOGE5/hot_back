<?php

namespace App\Filament\Widgets;

use App\Models\Ingrediente;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class IngredientesCriticosWidget extends TableWidget
{
    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Ingredientes críticos';

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, ['SUPER ADMIN', 'ADMIN', 'CHEF'], true);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getIngredientesCriticosQuery())
            ->columns([
                TextColumn::make('nombre')
                    ->label('Ingrediente')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('unidad_medida')
                    ->label('Unidad')
                    ->badge(),

                TextColumn::make('stock_actual')
                    ->label('Stock actual')
                    ->numeric(decimalPlaces: 2)
                    ->color('danger'),

                TextColumn::make('stock_minimo')
                    ->label('Stock mínimo')
                    ->numeric(decimalPlaces: 2)
                    ->color('warning'),

                TextColumn::make('estado_visual')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Agotado' => 'danger',
                        'Bajo stock' => 'warning',
                        'Vencido' => 'gray',
                        default => 'primary',
                    }),
            ])
            ->defaultSort('stock_actual')
            ->paginated(false);
    }

    private function getIngredientesCriticosQuery(): Builder
    {
        return Ingrediente::query()
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->orderBy('stock_actual')
            ->orderBy('nombre')
            ->limit(5);
    }
}

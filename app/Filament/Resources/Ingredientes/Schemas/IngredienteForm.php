<?php

namespace App\Filament\Resources\Ingredientes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IngredienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del ingrediente')
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        Select::make('unidad_medida')
                            ->label('Unidad de medida')
                            ->required()
                            ->options([
                                'kg' => 'kg',
                                'g' => 'g',
                                'l' => 'l',
                                'ml' => 'ml',
                                'unidad' => 'unidad',
                                'paquete' => 'paquete',
                            ]),

                        TextInput::make('proveedor')
                            ->label('Proveedor')
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Section::make('Stock y costos')
                    ->schema([
                        TextInput::make('stock_actual')
                            ->label('Stock actual')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step('0.01'),

                        TextInput::make('stock_minimo')
                            ->label('Stock mínimo')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step('0.01'),

                        TextInput::make('costo_unitario')
                            ->label('Costo unitario')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step('0.01'),

                        DatePicker::make('fecha_vencimiento')
                            ->label('Fecha de vencimiento')
                            ->native(false),
                    ])
                    ->columns(4),

                Section::make('Observación')
                    ->schema([
                        Textarea::make('observacion')
                            ->label('Observación')
                            ->rows(3)
                            ->maxLength(1000),
                    ]),
            ]);
    }
}
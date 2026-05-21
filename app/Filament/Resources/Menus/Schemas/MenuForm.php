<?php

namespace App\Filament\Resources\Menus\Schemas;

use App\Models\Plato;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del menu')
                    ->schema([
                        DatePicker::make('fecha_menu')
                            ->label('Fecha del menu')
                            ->required()
                            ->minDate(today())
                            ->rule('after_or_equal:today')
                            ->validationMessages([
                                'after_or_equal' => 'La fecha del menú debe ser hoy o una fecha futura.',
                            ]),

                        Select::make('tipo_menu')
                            ->label('Tipo de menu')
                            ->required()
                            ->options([
                                'Desayuno' => 'Desayuno',
                                'Almuerzo' => 'Almuerzo',
                                'Cena' => 'Cena',
                                'Especial del día' => 'Especial del día',
                            ]),

                        Select::make('estado')
                            ->label('Estado')
                            ->required()
                            ->default('Borrador')
                            ->options([
                                'Borrador' => 'Borrador',
                                'Publicado' => 'Publicado',
                                'Archivado' => 'Archivado',
                            ])
                            ->rules([
                                fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $platosData = $get('platos_menu');

                                    if (! empty($platosData) && is_array($platosData)) {
                                        $platosSeleccionados = [];

                                        foreach ($platosData as $item) {
                                            if (empty($item['plato_id'])) {
                                                continue;
                                            }

                                            if (in_array($item['plato_id'], $platosSeleccionados, true)) {
                                                $fail('No se puede seleccionar el mismo plato mas de una vez.');
                                                return;
                                            }

                                            $platosSeleccionados[] = $item['plato_id'];
                                        }
                                    }

                                    if ($value !== 'Publicado') {
                                        return;
                                    }

                                    if (empty($platosData) || ! is_array($platosData)) {
                                        $fail('No se puede publicar un menu sin platos.');
                                        return;
                                    }

                                    foreach ($platosData as $item) {
                                        if (empty($item['plato_id'])) {
                                            $fail('Todos los platos del menu deben estar seleccionados.');
                                            return;
                                        }

                                        $plato = Plato::query()
                                            ->with('ingredientes')
                                            ->find($item['plato_id']);

                                        if (! $plato) {
                                            $fail('Uno de los platos seleccionados no existe.');
                                            return;
                                        }

                                        if ($plato->estado !== 'Disponible') {
                                            $fail("El menu no puede publicarse porque el plato '{$plato->nombre}' no esta Disponible.");
                                            return;
                                        }

                                        if ($plato->ingredientes->isEmpty()) {
                                            $fail("El menu no puede publicarse porque el plato '{$plato->nombre}' no tiene ingredientes.");
                                            return;
                                        }

                                        if (! $plato->tieneStockSuficiente()) {
                                            $fail("El menu no puede publicarse porque el plato '{$plato->nombre}' no tiene stock suficiente.");
                                            return;
                                        }
                                    }
                                },
                            ]),

                        Select::make('chef_id')
                            ->label('Chef responsable')
                            ->relationship('chef', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Textarea::make('observacion')
                            ->label('Observacion')
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Platos del menu')
                    ->schema([
                        Repeater::make('platos_menu')
                            ->label('Platos')
                            ->schema([
                                Select::make('plato_id')
                                    ->label('Plato')
                                    ->required()
                                    ->rules(['exists:platos,id'])
                                    ->options(function (): array {
                                        return Plato::query()
                                            ->with('ingredientes')
                                            ->where('estado', '!=', 'Archivado')
                                            ->orderBy('nombre')
                                            ->get()
                                            ->mapWithKeys(function (Plato $plato): array {
                                                return [
                                                    $plato->id => "{$plato->nombre} - {$plato->categoria} - Bs. {$plato->precio} - {$plato->estado} - {$plato->estado_stock_visual}",
                                                ];
                                            })
                                            ->toArray();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                TextInput::make('orden')
                                    ->label('Orden')
                                    ->required()
                                    ->integer()
                                    ->default(1)
                                    ->minValue(1)
                                    ->validationMessages([
                                        'required' => 'El orden es obligatorio.',
                                        'integer' => 'El orden debe ser un número entero.',
                                        'min' => 'El orden debe ser mayor que 0.',
                                    ]),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Agregar plato')
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

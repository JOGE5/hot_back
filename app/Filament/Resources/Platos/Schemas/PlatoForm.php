<?php

namespace App\Filament\Resources\Platos\Schemas;

use App\Models\Ingrediente;
use App\Models\User;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PlatoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del plato')
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        Select::make('categoria')
                            ->label('Categoría')
                            ->required()
                            ->options([
                                'Desayuno' => 'Desayuno',
                                'Almuerzo' => 'Almuerzo',
                                'Cena' => 'Cena',
                                'Bebida' => 'Bebida',
                                'Postre' => 'Postre',
                                'Especial' => 'Especial',
                            ]),

                        Select::make('estado')
                            ->label('Estado')
                            ->required()
                            ->default('Disponible')
                            ->options([
                                'Disponible' => 'Disponible',
                                'No disponible' => 'No disponible',
                                'En preparación' => 'En preparación',
                                'Archivado' => 'Archivado',
                            ])
                            ->rules([
                                fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    if ($value !== 'Disponible') {
                                        return;
                                    }

                                    $ingredientesData = $get('ingredientes_receta');

                                    if (empty($ingredientesData) || ! is_array($ingredientesData)) {
                                        $fail('El plato no puede estar Disponible si no tiene ingredientes requeridos registrados.');
                                        return;
                                    }

                                    $ingredientesSeleccionados = [];

                                    foreach ($ingredientesData as $item) {
                                        if (empty($item['ingrediente_id']) || empty($item['cantidad_requerida'])) {
                                            $fail('Todos los ingredientes requeridos deben tener ingrediente y cantidad.');
                                            return;
                                        }

                                        if (in_array($item['ingrediente_id'], $ingredientesSeleccionados, true)) {
                                            $fail('No se puede seleccionar el mismo ingrediente mas de una vez.');
                                            return;
                                        }

                                        $ingredientesSeleccionados[] = $item['ingrediente_id'];

                                        if ((float) $item['cantidad_requerida'] <= 0) {
                                            $fail('La cantidad requerida de cada ingrediente debe ser mayor a 0.');
                                            return;
                                        }

                                        $ingrediente = Ingrediente::find($item['ingrediente_id']);

                                        if (! $ingrediente) {
                                            $fail('Uno de los ingredientes seleccionados no existe.');
                                            return;
                                        }

                                        if ($ingrediente->estado_visual === 'Vencido') {
                                            $fail("El plato no puede estar Disponible porque el ingrediente '{$ingrediente->nombre}' está vencido.");
                                            return;
                                        }

                                        if ($ingrediente->estado_visual === 'Agotado') {
                                            $fail("El plato no puede estar Disponible porque el ingrediente '{$ingrediente->nombre}' está agotado.");
                                            return;
                                        }

                                        if ((float) $ingrediente->stock_actual < (float) $item['cantidad_requerida']) {
                                            $fail("El plato no puede estar Disponible porque no hay stock suficiente de '{$ingrediente->nombre}'.");
                                            return;
                                        }
                                    }
                                },
                            ]),

                        Select::make('chef_id')
                            ->label('Chef responsable')
                            ->options(fn (): array => User::query()
                                ->where('estado', true)
                                ->whereHas('role', fn ($query) => $query->where('nombre', 'CHEF'))
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->rules([
                                function (string $attribute, mixed $value, Closure $fail): void {
                                    $esChefActivo = User::query()
                                        ->whereKey($value)
                                        ->where('estado', true)
                                        ->whereHas('role', fn ($query) => $query->where('nombre', 'CHEF'))
                                        ->exists();

                                    if (! $esChefActivo) {
                                        $fail('El chef responsable es obligatorio y debe ser un usuario activo con rol CHEF.');
                                    }
                                },
                            ])
                            ->validationMessages([
                                'required' => 'El chef responsable es obligatorio y debe ser un usuario activo con rol CHEF.',
                            ]),
                    ])
                    ->columns(2),

                Section::make('Detalle gastronómico')
                    ->schema([
                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->nullable()
                            ->columnSpanFull(),

                        TextInput::make('precio')
                            ->label('Precio')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->step('0.01'),

                        TextInput::make('tiempo_preparacion')
                            ->label('Tiempo de preparación en minutos')
                            ->nullable()
                            ->numeric()
                            ->minValue(1)
                            ->step(1),

                        FileUpload::make('imagen')
                            ->label('Imagen del plato')
                            ->image()
                            ->disk('public')
                            ->directory('platos')
                            ->visibility('public')
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'image/jpg',
                                'image/jfif',
                            ])
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                $extension = $file->getClientOriginalExtension();
                                $nombreArchivo = uniqid('plato_', true) . '.' . $extension;

                                return $file->storeAs('platos', $nombreArchivo, 'public');
                            })
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Ingredientes requeridos')
                    ->schema([
                        Repeater::make('ingredientes_receta')
                            ->label('Ingredientes del plato')
                            ->schema([
                                Select::make('ingrediente_id')
                                    ->label('Ingrediente')
                                    ->required()
                                    ->rules(['exists:ingredientes,id'])
                                    ->options(function (): array {
                                        return Ingrediente::query()
                                            ->orderBy('nombre')
                                            ->get()
                                            ->mapWithKeys(function (Ingrediente $ingrediente): array {
                                                return [
                                                    $ingrediente->id => "{$ingrediente->nombre} ({$ingrediente->unidad_medida}) - Stock: {$ingrediente->stock_actual}",
                                                ];
                                            })
                                            ->toArray();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                TextInput::make('cantidad_requerida')
                                    ->label('Cantidad requerida')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0.01)
                                    ->step('0.01'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Agregar ingrediente')
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ]),

                Section::make('Observación')
                    ->schema([
                        Textarea::make('observacion')
                            ->label('Observación')
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

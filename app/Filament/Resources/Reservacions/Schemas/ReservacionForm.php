<?php

namespace App\Filament\Resources\Reservacions\Schemas;

use App\Models\Habitacion;
use App\Models\Reservacion;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;

class ReservacionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Datos del Huésped')
                    ->schema([
                        Hidden::make('origen_reservacion')->default('Recepción presencial'),
                        Select::make('huesped_id')
                            ->label('Huésped')
                            ->relationship(
                                name: 'huesped',
                                modifyQueryUsing: fn (Builder $query) => $query->where('estado', true),
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nombres} {$record->apellido_paterno} {$record->apellido_materno} - {$record->numero_documento}")
                            ->searchable(['nombres', 'apellido_paterno', 'apellido_materno', 'numero_documento', 'correo_electronico'])
                            ->required()
                            ->columnSpanFull()
                            ->createOptionForm([
                                TextInput::make('nombres')
                                    ->label('Nombres')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('apellido_paterno')
                                    ->label('Apellido Paterno')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('apellido_materno')
                                    ->label('Apellido Materno')
                                    ->maxLength(255),
                                Select::make('tipo_documento')
                                    ->label('Tipo de Documento')
                                    ->options([
                                        'CI' => 'Cédula de Identidad',
                                        'Pasaporte' => 'Pasaporte',
                                        'DNI' => 'DNI',
                                        'Otro' => 'Otro',
                                    ])
                                    ->required(),
                                TextInput::make('numero_documento')
                                    ->label('Número de Documento')
                                    ->required()
                                    ->unique('huespedes', 'numero_documento')
                                    ->maxLength(50),
                                TextInput::make('telefono')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(50),
                                TextInput::make('correo_electronico')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->maxLength(255),
                                DatePicker::make('fecha_nacimiento')
                                    ->label('Fecha de Nacimiento')
                                    ->required()
                                    ->maxDate(now()->subYears(21))
                                    ->validationMessages([
                                        'max' => 'El huésped debe ser mayor de 21 años.',
                                    ]),
                                Select::make('nacionalidad_select')
                                    ->label('Nacionalidad')
                                    ->options([
                                        'Bolivia' => 'Bolivia',
                                        'Argentina' => 'Argentina',
                                        'Brasil' => 'Brasil',
                                        'Chile' => 'Chile',
                                        'Colombia' => 'Colombia',
                                        'Ecuador' => 'Ecuador',
                                        'Paraguay' => 'Paraguay',
                                        'Perú' => 'Perú',
                                        'Uruguay' => 'Uruguay',
                                        'Venezuela' => 'Venezuela',
                                        'México' => 'México',
                                        'Estados Unidos' => 'Estados Unidos',
                                        'España' => 'España',
                                        'Otra' => 'Otra',
                                    ])
                                    ->required()
                                    ->live(),
                                TextInput::make('nacionalidad_otra')
                                    ->label('Especifique la Nacionalidad')
                                    ->required(fn (Get $get) => $get('nacionalidad_select') === 'Otra')
                                    ->visible(fn (Get $get) => $get('nacionalidad_select') === 'Otra')
                                    ->maxLength(100),
                                Hidden::make('estado')->default(true),
                            ])
                            ->createOptionAction(function (Action $action) {
                                return $action->mutateFormDataUsing(function (array $data) {
                                    if ($data['nacionalidad_select'] === 'Otra') {
                                        $data['nacionalidad'] = $data['nacionalidad_otra'] ?? 'Otra';
                                    } else {
                                        $data['nacionalidad'] = $data['nacionalidad_select'];
                                    }
                                    unset($data['nacionalidad_select'], $data['nacionalidad_otra']);
                                    return $data;
                                });
                            }),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                Section::make('Datos de la Reservación')
                    ->schema([
                        TextInput::make('cantidad_personas')
                            ->label('Cantidad de Personas')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(function (Get $get) {
                                $habitacionId = $get('habitacion_id');
                                if ($habitacionId) {
                                    $habitacion = Habitacion::find($habitacionId);
                                    return $habitacion ? $habitacion->capacidad : 10;
                                }
                                return 10;
                            })
                            ->rule(function (Get $get) {
                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $habitacionId = $get('habitacion_id');
                                    if ($habitacionId) {
                                        $habitacion = Habitacion::find($habitacionId);
                                        if ($habitacion && $value > $habitacion->capacidad) {
                                            $fail("La cantidad de personas no puede superar la capacidad de la habitación seleccionada.");
                                        }
                                    }
                                };
                            }),

                        DatePicker::make('fecha_entrada')
                            ->label('Fecha de Entrada')
                            ->required()
                            ->minDate(now()->startOfDay())
                            ->maxDate(now()->addYears(2))
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                $set('habitacion_id', null);
                                if ($state) {
                                    $salida = $get('fecha_salida');
                                    if ($salida) {
                                        $fechaSalida = Carbon::parse($salida);
                                        $fechaEntrada = Carbon::parse($state);
                                        if ($fechaSalida->lessThanOrEqualTo($fechaEntrada)) {
                                            $set('fecha_salida', null);
                                        } elseif ($fechaSalida->diffInYears($fechaEntrada) > 2) {
                                            $set('fecha_salida', null);
                                        }
                                    }
                                }
                                self::updateTotal($set, $get);
                            }),

                        DatePicker::make('fecha_salida')
                            ->label('Fecha de Salida')
                            ->required()
                            ->minDate(fn (Get $get) => $get('fecha_entrada') ? Carbon::parse($get('fecha_entrada'))->addDay() : now()->addDay())
                            ->maxDate(fn (Get $get) => $get('fecha_entrada') ? Carbon::parse($get('fecha_entrada'))->addYears(2) : now()->addYears(2))
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $set('habitacion_id', null);
                                self::updateTotal($set, $get);
                            }),

                        TextInput::make('total')
                            ->label('Total a pagar')
                            ->required()
                            ->numeric()
                            ->disabled()
                            ->dehydrated(true)
                            ->default(0.0)
                            ->extraInputAttributes(['class' => 'bg-gray-100 dark:bg-gray-800'])
                            ->helperText('El total se calcula automáticamente.'),

                        Select::make('metodo_pago')
                            ->label('Método de Pago')
                            ->options([
                                'Efectivo' => 'Efectivo',
                                'QR' => 'QR',
                                'Transferencia' => 'Transferencia',
                                'Tarjeta' => 'Tarjeta',
                            ])
                            ->required(fn (Get $get) => $get('origen_reservacion') === 'Recepción presencial'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Selección de Habitación')
                    ->description('Utilice los filtros auxiliares para encontrar rápidamente una habitación disponible.')
                    ->schema([
                        Select::make('filtro_tipo_habitacion')
                            ->label('Filtrar Tipo')
                            ->options([
                                'Todos' => 'Todos',
                                'Simple' => 'Simple',
                                'Doble' => 'Doble',
                                'Matrimonial' => 'Matrimonial',
                                'Familiar' => 'Familiar',
                                'Suite' => 'Suite',
                            ])
                            ->default('Todos')
                            ->dehydrated(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $set('habitacion_id', null);
                                $set('cantidad_personas', null);
                                self::updateTotal($set, $get);
                            }),

                        Select::make('filtro_capacidad_minima')
                            ->label('Capacidad mínima')
                            ->options([
                                'Todos' => 'Todos',
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                                '4' => '4',
                                '5' => '5 o más',
                            ])
                            ->default('Todos')
                            ->dehydrated(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $set('habitacion_id', null);
                                $set('cantidad_personas', null);
                                self::updateTotal($set, $get);
                            }),

                        Select::make('habitacion_id')
                            ->label('Habitación')
                            ->options(function (Get $get, ?Reservacion $record = null) {
                                $query = Habitacion::query()
                                    ->where('activo', true)
                                    ->where('estado', 'Disponible');
                                
                                $tipo = $get('filtro_tipo_habitacion');
                                if ($tipo && $tipo !== 'Todos') {
                                    $query->where('tipo', $tipo);
                                }

                                $capacidad = $get('filtro_capacidad_minima');
                                if ($capacidad && $capacidad !== 'Todos') {
                                    if ($capacidad === '5') {
                                        $query->where('capacidad', '>=', 5);
                                    } else {
                                        $query->where('capacidad', '>=', (int) $capacidad);
                                    }
                                }

                                $fechaEntrada = $get('fecha_entrada');
                                $fechaSalida = $get('fecha_salida');
                                if ($fechaEntrada && $fechaSalida) {
                                    $query->whereDoesntHave('reservaciones', function ($q) use ($fechaEntrada, $fechaSalida, $record) {
                                        $q->conConflictoDisponibilidad(
                                            $fechaEntrada,
                                            $fechaSalida,
                                            $record?->id,
                                        );
                                    });
                                }

                                $count = (clone $query)->count();

                                $opciones = $query->get()->mapWithKeys(function ($record) {
                                    return [
                                        $record->id => "Habitación {$record->numero} - {$record->tipo} - Bs. " . number_format($record->precio_noche, 2) . "/noche - Capacidad {$record->capacidad}"
                                    ];
                                })->toArray();

                                logger()->info('Opciones habitaciones reserva', [
                                    'tipo' => $tipo,
                                    'capacidad' => $capacidad,
                                    'entrada' => $fechaEntrada,
                                    'salida' => $fechaSalida,
                                    'cantidad_resultados' => $count,
                                ]);

                                return $opciones;
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->columnSpanFull()
                            ->rule(function () {
                                return function (string $attribute, $value, \Closure $fail) {
                                    if ($value) {
                                        $habitacion = Habitacion::find($value);
                                        if ($habitacion && in_array($habitacion->estado, ['Ocupada', 'Mantenimiento', 'Inactiva'])) {
                                            $fail("No se puede seleccionar una habitación que se encuentre en estado: {$habitacion->estado}.");
                                        }
                                    }
                                };
                            })
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $habitacionId = $get('habitacion_id');
                                if ($habitacionId) {
                                    $habitacion = Habitacion::find($habitacionId);
                                    if ($habitacion) {
                                        $cantidad = $get('cantidad_personas');
                                        if ($cantidad && $cantidad > $habitacion->capacidad) {
                                            $set('cantidad_personas', null);
                                        }
                                    }
                                }
                                self::updateTotal($set, $get);
                            }),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Estado y Observaciones')
                    ->schema([
                        Select::make('estado_pago')
                            ->label('Estado del Pago')
                            ->options([
                                'Pendiente' => 'Pendiente',
                                'Confirmado' => 'Confirmado',
                                'Rechazado' => 'Rechazado',
                            ])
                            ->required()
                            ->default('Pendiente')
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                if ($state === 'Confirmado' && $get('estado_reservacion') === 'Pendiente de pago') {
                                    $set('estado_reservacion', 'Confirmada');
                                }
                            }),

                        Select::make('estado_reservacion')
                            ->label('Estado de Reservación')
                            ->options([
                                'Pendiente de pago' => 'Pendiente de pago',
                                'Confirmada' => 'Confirmada',
                                'En estadía' => 'En estadía',
                                'Cancelada' => 'Cancelada',
                                'Finalizada' => 'Finalizada',
                            ])
                            ->default('Pendiente de pago')
                            ->visible(fn (string $context) => $context === 'edit' || $context === 'view')
                            ->rule(function (Get $get) {
                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                    if ($value === 'Confirmada' && $get('estado_pago') !== 'Confirmado') {
                                        $fail('No se puede confirmar la reservación si el pago no está confirmado.');
                                    }
                                };
                            }),
                            
                        TextInput::make('codigo_checkin')
                            ->label('Código Check-in')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Se genera automáticamente al confirmar el pago.')
                            ->visible(fn (string $context) => $context === 'edit' || $context === 'view')
                            ->columnSpanFull(),

                        Textarea::make('observacion')
                            ->label('Observación')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    protected static function updateTotal(Set $set, Get $get)
    {
        $habitacionId = $get('habitacion_id');
        $fechaEntrada = $get('fecha_entrada');
        $fechaSalida = $get('fecha_salida');

        if ($habitacionId && $fechaEntrada && $fechaSalida) {
            try {
                $inicio = Carbon::parse($fechaEntrada);
                $fin = Carbon::parse($fechaSalida);
                
                if ($fin->greaterThan($inicio)) {
                    $noches = $inicio->diffInDays($fin);
                    $habitacion = Habitacion::find($habitacionId);
                    
                    if ($habitacion && $noches > 0) {
                        $set('total', $habitacion->precio_noche * $noches);
                        return;
                    }
                }
            } catch (\Exception $e) {
                // Ignore parsing errors
            }
        }
        
        $set('total', 0);
    }
}

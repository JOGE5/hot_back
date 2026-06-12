<?php

namespace App\Filament\Resources\Consumos\Schemas;

use App\Models\Habitacion;
use App\Models\Huesped;
use App\Models\Plato;
use App\Models\Reservacion;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ConsumoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('reservacion_id')
                ->label('Reservación')
                ->searchable()
                ->preload()
                ->nullable()
                ->reactive()
                ->afterStateUpdated(function ($set, $state) {
                    if ($state) {
                        $reservacion = Reservacion::with(['huesped', 'habitacion'])->find($state);
                        if ($reservacion) {
                            if ($reservacion->huesped_id) {
                                $set('huesped_id', $reservacion->huesped_id);
                            }
                            if ($reservacion->habitacion_id) {
                                $set('habitacion_id', $reservacion->habitacion_id);
                            }
                        }
                    }
                })
                ->options(function () {
                    return Reservacion::with(['huesped', 'habitacion'])
                        ->get()
                        ->mapWithKeys(function (Reservacion $reservacion) {
                            $codigo = $reservacion->codigo_checkin ?: 'Sin código';
                            $huesped = $reservacion->huesped
                                ? trim(
                                    ($reservacion->huesped->nombres ?? '') . ' ' .
                                    ($reservacion->huesped->apellido_paterno ?? '') . ' ' .
                                    ($reservacion->huesped->apellido_materno ?? '')
                                )
                                : 'Huésped no disponible';
                            $habitacion = $reservacion->habitacion?->numero ?: 'Habitación no disponible';

                            return [$reservacion->id => "{$codigo} — {$huesped} — Hab. {$habitacion}"];
                        })
                        ->toArray();
                }),

            Select::make('huesped_id')
                ->label('Huésped')
                ->searchable()
                ->preload()
                ->nullable()
                ->options(function () {
                    return Huesped::query()
                        ->get()
                        ->mapWithKeys(function (Huesped $huesped) {
                            $nombre = trim(
                                ($huesped->nombres ?? '') . ' ' .
                                ($huesped->apellido_paterno ?? '') . ' ' .
                                ($huesped->apellido_materno ?? '')
                            );

                            return [$huesped->id => $nombre ?: 'Huésped sin nombre'];
                        })
                        ->toArray();
                }),

            Select::make('habitacion_id')
                ->label('Habitación')
                ->searchable()
                ->preload()
                ->nullable()
                ->options(function () {
                    return Habitacion::query()
                        ->get()
                        ->mapWithKeys(function (Habitacion $habitacion) {
                            return [$habitacion->id => "Habitación {$habitacion->numero}"];
                        })
                        ->toArray();
                }),

            Select::make('plato_id')
                ->label('Plato')
                ->relationship('plato', 'nombre')
                ->searchable()
                ->preload()
                ->required()
                ->rules(['exists:platos,id'])
                ->reactive()
                ->afterStateUpdated(function ($set, $state) {
                    $plato = Plato::find($state);

                    if ($plato) {
                        $set('precio_unitario', $plato->precio);
                    }
                }),

            TextInput::make('cantidad')
                ->label('Cantidad')
                ->numeric()
                ->required()
                ->rules(['integer', 'min:1'])
                ->minValue(1)
                ->default(1)
                ->reactive()
                ->afterStateUpdated(function ($set, $get) {
                    $cantidad = $get('cantidad') ?? 1;
                    $precio = $get('precio_unitario') ?? 0;

                    $set('total', $cantidad * $precio);
                }),

            TextInput::make('precio_unitario')
                ->label('Precio unitario (Bs.)')
                ->numeric()
                ->required()
                ->rules(['numeric', 'min:0.01'])
                ->minValue(0.01)
                ->reactive()
                ->afterStateUpdated(function ($set, $get) {
                    $cantidad = $get('cantidad') ?? 1;
                    $precio = $get('precio_unitario') ?? 0;

                    $set('total', $cantidad * $precio);
                }),

            TextInput::make('total')
                ->label('Total (Bs.)')
                ->numeric()
                ->disabled()
                ->dehydrated(true)
                ->required()
                ->rules(['numeric', 'min:0.01'])
                ->minValue(0.01)
                ->default(0)
                ->helperText('Se calcula automáticamente como cantidad × precio unitario.'),

            DateTimePicker::make('fecha_consumo')
                ->label('Fecha de consumo')
                ->required()
                ->default(now()),

            Select::make('estado')
                ->label('Estado')
                ->options([
                    'Pendiente' => 'Pendiente',
                    'Confirmado' => 'Confirmado',
                    'Anulado' => 'Anulado',
                ])
                ->default('Confirmado')
                ->required()
                ->rules(['in:Pendiente,Confirmado,Anulado']),

            Textarea::make('observacion')
                ->label('Observación')
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }
}


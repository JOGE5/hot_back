<?php

namespace App\Filament\Resources\Pagos\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use App\Models\Reservacion;
use App\Models\Pago;
use Closure;

class PagoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('reservacion_id')
                    ->label('Reservación')
                    ->relationship(
                        name: 'reservacion',
                        titleAttribute: 'codigo_checkin',
                        modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query, ?Pago $record) => $query->when(
                            !$record,
                            fn ($q) => $q->where('estado_pago', 'Pendiente')
                                         ->whereNotIn('estado_reservacion', ['Finalizada', 'Cancelada', 'En estadía'])
                        )
                    )
                    ->getOptionLabelFromRecordUsing(fn (Reservacion $record) => "Reserva #{$record->id} - {$record->huesped->nombres} {$record->huesped->apellido_paterno} (Total: Bs. {$record->total})")
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn (?Pago $record) => $record !== null), // Bloquear edición de reservación una vez creado
                
                TextInput::make('monto')
                    ->label('Monto (Bs.)')
                    ->required()
                    ->numeric()
                    ->minValue(0.01)
                    ->disabled(fn (?Pago $record) => $record && $record->estado_pago === 'Confirmado')
                    ->rules([
                        fn (Get $get) => function (string $attribute, $value, Closure $fail) use ($get) {
                            $reservacion = Reservacion::find($get('reservacion_id'));
                            if (!$reservacion) return;

                            if ($value > $reservacion->total) {
                                $fail('El monto no puede ser mayor al total de la reservación (Bs. ' . $reservacion->total . ').');
                            }

                            if ($get('estado_pago') === 'Confirmado' && (float)$value !== (float)$reservacion->total) {
                                $fail('Para confirmar el pago, el monto debe ser exactamente igual al total de la reservación (Bs. ' . $reservacion->total . ').');
                            }
                        },
                    ]),

                Select::make('metodo_pago')
                    ->label('Método de pago')
                    ->options([
                        'Efectivo' => 'Efectivo',
                        'QR' => 'QR',
                        'Transferencia' => 'Transferencia',
                        'Tarjeta' => 'Tarjeta',
                    ])
                    ->required(),

                Select::make('estado_pago')
                    ->label('Estado del pago')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'Confirmado' => 'Confirmado',
                        'Rechazado' => 'Rechazado',
                    ])
                    ->default('Pendiente')
                    ->required()
                    ->rules([
                        fn (Get $get, ?Pago $record) => function (string $attribute, $value, Closure $fail) use ($get, $record) {
                            $reservacion = Reservacion::find($get('reservacion_id'));
                            if (!$reservacion) return;

                            if ($value === 'Confirmado') {
                                if (in_array($reservacion->estado_reservacion, ['Finalizada', 'Cancelada', 'En estadía'])) {
                                    $fail('No se puede confirmar el pago porque la reservación ya está ' . $reservacion->estado_reservacion . '.');
                                }

                                // Check if there is already a confirmed payment for this reservation
                                $query = Pago::where('reservacion_id', $reservacion->id)
                                    ->where('estado_pago', 'Confirmado');
                                
                                if ($record) {
                                    $query->where('id', '!=', $record->id);
                                }

                                if ($query->exists()) {
                                    $fail('Ya existe un pago confirmado para esta reservación.');
                                }
                            }
                        },
                    ]),

                DateTimePicker::make('fecha_pago')
                    ->label('Fecha de pago')
                    ->default(now()),

                TextInput::make('comprobante')
                    ->label('Comprobante (Nro. de recibo o transacción)')
                    ->maxLength(255),

                Textarea::make('observacion')
                    ->label('Observación')
                    ->columnSpanFull(),
            ]);
    }
}

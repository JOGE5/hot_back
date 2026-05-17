<?php

namespace App\Filament\Resources\Reservacions\Pages;

use App\Filament\Resources\Reservacions\ReservacionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditReservacion extends EditRecord
{
    protected static string $resource = ReservacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['filtro_tipo_habitacion'], $data['filtro_estado_habitacion']);

        $habitacionId = $data['habitacion_id'] ?? null;
        $huespedId = $data['huesped_id'] ?? null;
        $fechaEntrada = $data['fecha_entrada'] ?? null;
        $fechaSalida = $data['fecha_salida'] ?? null;
        $cantidadPersonas = $data['cantidad_personas'] ?? null;

        if ($huespedId) {
            $tieneReservaActiva = \App\Models\Reservacion::query()
                ->where('huesped_id', $huespedId)
                ->where('id', '!=', $this->record->id)
                ->whereIn('estado_reservacion', ['Pendiente de pago', 'Confirmada', 'En estadía'])
                ->exists();

            if ($tieneReservaActiva) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'huesped_id' => 'Este huésped ya tiene una reservación activa. Debe finalizarla o cancelarla antes de crear otra.',
                ]);
            }
        }

        if ($habitacionId && $fechaEntrada && $fechaSalida) {
            $habitacion = \App\Models\Habitacion::find($habitacionId);

            if (!$habitacion) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'habitacion_id' => 'La habitación seleccionada no existe.',
                ]);
            }

            if (in_array($habitacion->estado, ['Mantenimiento', 'Inactiva'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'habitacion_id' => 'La habitación seleccionada no está disponible (Estado actual: ' . $habitacion->estado . ').',
                ]);
            }

            if ($cantidadPersonas > $habitacion->capacidad) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'cantidad_personas' => "La cantidad de personas ($cantidadPersonas) supera la capacidad máxima de la habitación ({$habitacion->capacidad}).",
                ]);
            }

            $inicio = \Carbon\Carbon::parse($fechaEntrada);
            $fin = \Carbon\Carbon::parse($fechaSalida);

            if ($fin->lessThanOrEqualTo($inicio)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'fecha_salida' => 'La fecha de salida debe ser mayor a la fecha de entrada.',
                ]);
            }

            if ($fin->diffInYears($inicio) > 2) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'fecha_salida' => 'La fecha de salida no puede exceder 2 años desde la fecha de entrada.',
                ]);
            }

            $cruce = \App\Models\Reservacion::query()
                ->where('habitacion_id', $habitacionId)
                ->where('id', '!=', $this->record->id)
                ->whereIn('estado_reservacion', ['Pendiente de pago', 'Confirmada', 'En estadía'])
                ->where('fecha_entrada', '<', $fechaSalida)
                ->where('fecha_salida', '>', $fechaEntrada)
                ->exists();

            if ($cruce) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'fecha_salida' => 'La habitación ya tiene una reservación activa en las fechas seleccionadas.',
                ]);
            }

            $noches = $inicio->diffInDays($fin);
            if ($noches <= 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'fecha_salida' => 'La cantidad de noches debe ser mayor a 0.',
                ]);
            }

            $data['total'] = $habitacion->precio_noche * $noches;
        } else {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'habitacion_id' => 'Faltan datos para la reservación (habitación, fechas).',
            ]);
        }

        $estadoPago = $data['estado_pago'] ?? 'Pendiente';
        $estadoActual = $data['estado_reservacion'] ?? 'Pendiente de pago';

        if ($estadoPago === 'Confirmado') {
            if ($estadoActual !== 'Finalizada' && $estadoActual !== 'En estadía') {
                $data['estado_reservacion'] = 'Confirmada';
            }
            if (empty($data['codigo_checkin'])) {
                $data['codigo_checkin'] = 'CHK-' . strtoupper(substr(uniqid(), -6));
            }
        } elseif ($estadoPago === 'Rechazado') {
            $data['estado_reservacion'] = 'Cancelada';
        } elseif ($estadoPago === 'Pendiente') {
            if ($estadoActual !== 'Cancelada' && $estadoActual !== 'Finalizada') {
                $data['estado_reservacion'] = 'Pendiente de pago';
            }
        }

        return $data;
    }
}

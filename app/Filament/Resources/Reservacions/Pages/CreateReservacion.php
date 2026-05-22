<?php

namespace App\Filament\Resources\Reservacions\Pages;

use App\Filament\Resources\Reservacions\ReservacionResource;
use App\Models\Habitacion;
use App\Models\Reservacion;
use App\Support\LogSistema;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateReservacion extends CreateRecord
{
    protected static string $resource = ReservacionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        logger()->info('Datos antes de crear reservacion', $data);

        unset($data['filtro_tipo_habitacion'], $data['filtro_estado_habitacion']);

        $habitacionId = $data['habitacion_id'] ?? null;
        $huespedId = $data['huesped_id'] ?? null;
        $fechaEntrada = $data['fecha_entrada'] ?? null;
        $fechaSalida = $data['fecha_salida'] ?? null;
        $cantidadPersonas = $data['cantidad_personas'] ?? null;

        if ($huespedId) {
            $tieneReservaActiva = Reservacion::query()
                ->where('huesped_id', $huespedId)
                ->bloqueantesDisponibilidad()
                ->exists();

            if ($tieneReservaActiva) {
                throw ValidationException::withMessages([
                    'huesped_id' => 'Este huésped ya tiene una reservación activa. Debe finalizarla o cancelarla antes de crear otra.',
                ]);
            }
        }

        if ($habitacionId && $fechaEntrada && $fechaSalida) {
            $habitacion = Habitacion::find($habitacionId);

            if (!$habitacion) {
                throw ValidationException::withMessages([
                    'habitacion_id' => 'La habitación seleccionada no existe.',
                ]);
            }

            if (in_array($habitacion->estado, ['Ocupada', 'Mantenimiento', 'Inactiva'])) {
                throw ValidationException::withMessages([
                    'habitacion_id' => 'La habitación seleccionada no está disponible (Estado actual: ' . $habitacion->estado . ').',
                ]);
            }

            if ($cantidadPersonas > $habitacion->capacidad) {
                throw ValidationException::withMessages([
                    'cantidad_personas' => "La cantidad de personas ($cantidadPersonas) supera la capacidad máxima de la habitación ({$habitacion->capacidad}).",
                ]);
            }

            $inicio = \Carbon\Carbon::parse($fechaEntrada);
            $fin = \Carbon\Carbon::parse($fechaSalida);

            if ($fin->lessThanOrEqualTo($inicio)) {
                throw ValidationException::withMessages([
                    'fecha_salida' => 'La fecha de salida debe ser mayor a la fecha de entrada.',
                ]);
            }

            if ($fin->diffInYears($inicio) > 2) {
                throw ValidationException::withMessages([
                    'fecha_salida' => 'La fecha de salida no puede exceder 2 años desde la fecha de entrada.',
                ]);
            }

            $cruce = Reservacion::habitacionTieneConflictoDisponibilidad($habitacionId, $fechaEntrada, $fechaSalida);

            if ($cruce) {
                throw ValidationException::withMessages([
                    'fecha_salida' => Reservacion::MENSAJE_HABITACION_NO_DISPONIBLE,
                ]);
            }

            $noches = $inicio->diffInDays($fin);
            if ($noches <= 0) {
                throw ValidationException::withMessages([
                    'fecha_salida' => 'La cantidad de noches debe ser mayor a 0.',
                ]);
            }

            $data['total'] = $habitacion->precio_noche * $noches;
        } else {
            throw ValidationException::withMessages([
                'habitacion_id' => 'Faltan datos para la reservación (habitación, fechas).',
            ]);
        }

        $data['origen_reservacion'] = $data['origen_reservacion'] ?? 'Recepción presencial';

        $estadoPago = $data['estado_pago'] ?? 'Pendiente';
        if ($estadoPago === 'Confirmado') {
            $data['estado_reservacion'] = 'Confirmada';
            if (empty($data['codigo_checkin'])) {
                $data['codigo_checkin'] = 'CHK-' . strtoupper(substr(uniqid(), -6));
            }
        } elseif ($estadoPago === 'Rechazado') {
            $data['estado_reservacion'] = 'Cancelada';
        } else {
            $data['estado_reservacion'] = 'Pendiente de pago';
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->loadMissing(['huesped', 'habitacion']);

        LogSistema::registrar(
            'CREAR',
            'Reservaciones',
            'Reservación creada #' . $this->record->id . ' para huésped ' . ($this->record->huesped?->nombres ?? 'Sin huésped') . ' en habitación ' . ($this->record->habitacion?->numero ?? 'Sin habitación') . '.'
        );

        if ($this->record->estado_pago === 'Confirmado') {
            LogSistema::registrar(
                'CONFIRMAR_PAGO',
                'Pagos',
                'Pago confirmado al crear reservación #' . $this->record->id . ' por Bs. ' . $this->record->total . '.'
            );
        }
    }
}

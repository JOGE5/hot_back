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
        $habitacion = null;

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

        // validar acompañantes y calcular cantidad_personas
        $this->validarAcompanantes($data, $huespedId, $habitacion);

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

    private function validarAcompanantes(array &$data, ?int $huespedId, ?Habitacion $habitacion): void
    {
        $acompanantes = $data['acompanantes'] ?? [];

        if (empty($acompanantes)) {
            $data['cantidad_personas'] = 1;
            return;
        }

        // comprobar titular
        $titularDocumento = null;
        if ($huespedId) {
            $huesped = \App\Models\Huesped::find($huespedId);
            if ($huesped) {
                if (empty($huesped->fecha_nacimiento)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'huesped_id' => 'El huésped seleccionado no tiene fecha de nacimiento registrada. No se puede verificar la mayoría de edad.',
                    ]);
                }

                $edadTitular = \Carbon\Carbon::parse($huesped->fecha_nacimiento)->age;
                if ($edadTitular < 21) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'huesped_id' => 'El titular debe tener al menos 21 años.',
                    ]);
                }

                $titularDocumento = $huesped->numero_documento;
            }
        }

        // verificar campos obligatorios y duplicados
        $documentos = [];
        foreach ($acompanantes as $index => $row) {
            $nombre = trim($row['nombre'] ?? '');
            $documento = trim($row['documento'] ?? '');
            $edad = isset($row['edad']) ? (int) $row['edad'] : null;

            if ($nombre === '' || $documento === '' || $edad === null) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "acompanantes.{$index}" => 'Todos los acompañantes deben tener nombre, documento y edad.',
                ]);
            }

            if ($edad < 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "acompanantes.{$index}.edad" => 'La edad debe ser un número válido.',
                ]);
            }

            if ($titularDocumento && $documento === $titularDocumento) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "acompanantes.{$index}.documento" => 'El documento del acompañante no puede coincidir con el documento del titular.',
                ]);
            }

            if (in_array($documento, $documentos, true)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "acompanantes.{$index}.documento" => 'Documento duplicado entre acompañantes.',
                ]);
            }

            $documentos[] = $documento;
        }

        // límites por tipo de habitación
        $maxAcompanantes = 0;
        if ($habitacion) {
            $map = \App\Models\Reservacion::maxAcompanantesPorTipo();
            $maxAcompanantes = $map[$habitacion->tipo] ?? 0;
        }

        $count = count($acompanantes);
        if ($count > $maxAcompanantes) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'acompanantes' => "La cantidad de acompañantes ({$count}) excede el máximo permitido para la habitación seleccionada ({$maxAcompanantes}).",
            ]);
        }

        // actualizar cantidad_personas y comprobar capacidad
        $data['cantidad_personas'] = 1 + $count;
        if ($habitacion && $data['cantidad_personas'] > $habitacion->capacidad) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'cantidad_personas' => 'La cantidad total de personas supera la capacidad de la habitación.',
            ]);
        }
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

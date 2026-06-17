<?php

namespace App\Filament\Resources\Reservacions\Pages;

use App\Filament\Resources\Reservacions\ReservacionResource;
use App\Models\Habitacion;
use App\Models\Reservacion;
use App\Models\ReservacionAcompanante;
use App\Support\LogSistema;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateReservacion extends CreateRecord
{
    protected static string $resource = ReservacionResource::class;

    protected array $acompanantesData = [];

    protected int $cantidadPersonasFinal = 1;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $cantidadFormulario = (int) ($data['cantidad_personas'] ?? 1);
        $this->acompanantesData = collect($data['acompanantes'] ?? [])
            ->filter(fn ($acompanante) => filled($acompanante['nombre'] ?? $acompanante['nombre_completo'] ?? null) && filled($acompanante['documento'] ?? $acompanante['numero_documento'] ?? null))
            ->values()
            ->all();

        $cantidadPorAcompanantes = 1 + count($this->acompanantesData ?? []);
        $cantidadFinal = max($cantidadFormulario, $cantidadPorAcompanantes, 1);
        $this->cantidadPersonasFinal = $cantidadFinal;
        $data['cantidad_personas'] = $cantidadFinal;

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
                $this->bloquearCreacion(
                    'huesped_con_reserva_activa',
                    ['huesped_id' => 'Este huésped ya tiene una reservación activa. Debe finalizarla o cancelarla antes de crear otra.'],
                    $data
                );
            }
        }

        if ($habitacionId && $fechaEntrada && $fechaSalida) {
            $habitacion = Habitacion::find($habitacionId);

            if (!$habitacion) {
                $this->bloquearCreacion(
                    'habitacion_no_existe',
                    ['habitacion_id' => 'La habitación seleccionada no existe.'],
                    $data
                );
            }

            if (in_array($habitacion->estado, ['Ocupada', 'Mantenimiento', 'Inactiva'])) {
                $this->bloquearCreacion(
                    'habitacion_no_disponible_estado',
                    ['habitacion_id' => 'La habitación seleccionada no está disponible (Estado actual: ' . $habitacion->estado . ').'],
                    $data
                );
            }

            if ($cantidadPersonas > $habitacion->capacidadMaximaReservable()) {
                $this->bloquearCreacion(
                    'cantidad_supera_capacidad',
                    ['cantidad_personas' => "La cantidad de personas ($cantidadPersonas) supera la capacidad máxima de la habitación ({$habitacion->capacidad})."],
                    $data
                );
            }

            $inicio = \Carbon\Carbon::parse($fechaEntrada);
            $fin = \Carbon\Carbon::parse($fechaSalida);

            if ($fin->lessThanOrEqualTo($inicio)) {
                $this->bloquearCreacion(
                    'fecha_salida_invalida',
                    ['fecha_salida' => 'La fecha de salida debe ser mayor a la fecha de entrada.'],
                    $data
                );
            }

            if ($fin->diffInYears($inicio) > 2) {
                $this->bloquearCreacion(
                    'reserva_excede_dos_anios',
                    ['fecha_salida' => 'La fecha de salida no puede exceder 2 años desde la fecha de entrada.'],
                    $data
                );
            }

            $cruce = Reservacion::habitacionTieneConflictoDisponibilidad($habitacionId, $fechaEntrada, $fechaSalida);

            if ($cruce) {
                $this->bloquearCreacion(
                    'cruce_disponibilidad',
                    ['fecha_salida' => Reservacion::MENSAJE_HABITACION_NO_DISPONIBLE],
                    $data
                );
            }

            $noches = $inicio->diffInDays($fin);
            if ($noches <= 0) {
                $this->bloquearCreacion(
                    'noches_invalidas',
                    ['fecha_salida' => 'La cantidad de noches debe ser mayor a 0.'],
                    $data
                );
            }

            $data['total'] = $habitacion->precio_noche * $noches;
        } else {
            $this->bloquearCreacion(
                'faltan_datos_reservacion',
                ['habitacion_id' => 'Faltan datos para la reservación (habitación, fechas).'],
                $data
            );
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

    protected function handleRecordCreation(array $data): Model
    {
        $data['cantidad_personas'] = max((int) $this->cantidadPersonasFinal, 1);

        // Crear la reservación explícitamente para garantizar que se use $data corregido
        $reservacion = Reservacion::create($data);

        foreach ($this->acompanantesData as $acompanante) {
            ReservacionAcompanante::create([
                'reservacion_id' => $reservacion->id,
                'nombre_completo' => $acompanante['nombre_completo'] ?? $acompanante['nombre'] ?? null,
                'tipo_documento' => $acompanante['tipo_documento'] ?? null,
                'numero_documento' => $acompanante['numero_documento'] ?? $acompanante['documento'] ?? null,
                'nacionalidad' => $acompanante['nacionalidad'] ?? null,
                'fecha_nacimiento' => $acompanante['fecha_nacimiento'] ?? null,
                'edad' => $acompanante['edad'] ?? null,
                'nombre' => $acompanante['nombre'] ?? null,
                'documento' => $acompanante['documento'] ?? null,
            ]);
        }

        return $reservacion;
    }

    private function validarAcompanantes(array &$data, ?int $huespedId, ?Habitacion $habitacion): void
    {
        $acompanantes = $data['acompanantes'] ?? [];
        $titularDocumento = null;

        if ($huespedId) {
            $huesped = \App\Models\Huesped::find($huespedId);
            if ($huesped) {
                if (empty($huesped->fecha_nacimiento)) {
                    $this->bloquearCreacion(
                        'error_acompanantes',
                        ['huesped_id' => 'El huesped seleccionado no tiene fecha de nacimiento registrada.'],
                        $data
                    );
                }

                if (\Carbon\Carbon::parse($huesped->fecha_nacimiento)->age < 21) {
                    $this->bloquearCreacion(
                        'error_acompanantes',
                        ['huesped_id' => 'El titular debe tener al menos 21 anos.'],
                        $data
                    );
                }

                $titularDocumento = $huesped->numero_documento;
            }
        }

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
                    $this->bloquearCreacion(
                        'error_acompanantes',
                        ['huesped_id' => 'El huésped seleccionado no tiene fecha de nacimiento registrada. No se puede verificar la mayoría de edad.'],
                        $data
                    );
                }

                $edadTitular = \Carbon\Carbon::parse($huesped->fecha_nacimiento)->age;
                if ($edadTitular < 21) {
                    $this->bloquearCreacion(
                        'error_acompanantes',
                        ['huesped_id' => 'El titular debe tener al menos 21 anos.'],
                        $data
                    );
                }

                $titularDocumento = $huesped->numero_documento;
            }
        }

        // verificar campos obligatorios y duplicados
        $documentos = [];
        foreach ($acompanantes as $index => $row) {
            $nombre = trim($row['nombre_completo'] ?? $row['nombre'] ?? '');
            $tipoDocumento = trim($row['tipo_documento'] ?? '');
            $documento = trim($row['numero_documento'] ?? $row['documento'] ?? '');
            $nacionalidad = trim($row['nacionalidad'] ?? '');
            $fechaNacimiento = $row['fecha_nacimiento'] ?? null;
            $edad = $fechaNacimiento ? \Carbon\Carbon::parse($fechaNacimiento)->age : (isset($row['edad']) ? (int) $row['edad'] : null);

            if ($nombre === '') {
                $this->bloquearCreacion(
                    'error_acompanantes',
                    ["acompanantes.{$index}.nombre" => 'Complete el nombre del acompañante.'],
                    $data
                );
            }

            if ($tipoDocumento === '') {
                $this->bloquearCreacion(
                    'error_acompanantes',
                    ["acompanantes.{$index}.tipo_documento" => 'Seleccione el tipo de documento del acompañante.'],
                    $data
                );
            }

            if ($documento === '') {
                $this->bloquearCreacion(
                    'error_acompanantes',
                    ["acompanantes.{$index}.documento" => 'Ingrese el número de documento del acompañante.'],
                    $data
                );
            }

            if ($nacionalidad === '') {
                $this->bloquearCreacion(
                    'error_acompanantes',
                    ["acompanantes.{$index}.nacionalidad" => 'Seleccione la nacionalidad del acompañante.'],
                    $data
                );
            }

            if (empty($fechaNacimiento)) {
                $this->bloquearCreacion(
                    'error_acompanantes',
                    ["acompanantes.{$index}.fecha_nacimiento" => 'Seleccione la fecha de nacimiento del acompañante.'],
                    $data
                );
            }

            if ($edad < 0) {
                $this->bloquearCreacion(
                    'error_acompanantes',
                    ["acompanantes.{$index}.edad" => 'La edad debe ser un número válido.'],
                    $data
                );
            }

            if ($fechaNacimiento && \Carbon\Carbon::parse($fechaNacimiento)->isFuture()) {
                $this->bloquearCreacion(
                    'error_acompanantes',
                    ["acompanantes.{$index}.fecha_nacimiento" => 'La fecha de nacimiento no puede ser futura.'],
                    $data
                );
            }

            if ($titularDocumento && $documento === $titularDocumento) {
                $this->bloquearCreacion(
                    'documento_titular_repetido',
                    ["acompanantes.{$index}.documento" => 'El documento del acompañante no puede ser igual al documento del titular.'],
                    $data
                );
            }

            if (in_array($documento, $documentos, true)) {
                $this->bloquearCreacion(
                    'documento_acompanante_repetido',
                    ["acompanantes.{$index}.documento" => 'Hay documentos repetidos entre acompañantes.'],
                    $data
                );
            }

            $documentos[] = $documento;
            $acompanantes[$index]['nombre_completo'] = $nombre;
            $acompanantes[$index]['nombre'] = $nombre;
            $acompanantes[$index]['numero_documento'] = $documento;
            $acompanantes[$index]['documento'] = $documento;
            $acompanantes[$index]['edad'] = $edad;
        }

        $data['acompanantes'] = $acompanantes;

        // límites por tipo de habitación
        $maxAcompanantes = 0;
        if ($habitacion) {
            $map = \App\Models\Reservacion::maxAcompanantesPorTipo();
            $maxAcompanantes = $map[$habitacion->tipo] ?? 0;
        }

        $count = count($acompanantes);
        if ($count > $maxAcompanantes) {
            $this->bloquearCreacion(
                'error_acompanantes',
                ['acompanantes' => "La cantidad de acompañantes ({$count}) excede el máximo permitido para la habitación seleccionada ({$maxAcompanantes})."],
                $data
            );
        }

        // actualizar cantidad_personas y comprobar capacidad
        $data['cantidad_personas'] = 1 + $count;
        if ($habitacion && $data['cantidad_personas'] > $habitacion->capacidadMaximaReservable()) {
            $this->bloquearCreacion(
                'cantidad_supera_capacidad',
                ['cantidad_personas' => 'La cantidad total de personas supera la capacidad de la habitación.'],
                $data
            );
        }
    }

    private function bloquearCreacion(string $motivo, array $messages, array $data): never
    {
        logger()->warning('Bloqueo al crear reservacion', [
            'motivo' => $motivo,
            'data' => $data,
            'mensajes' => $messages,
        ]);

        Notification::make()
            ->title('No se pudo crear la reservación')
            ->body(array_values($messages)[0])
            ->danger()
            ->send();

        throw ValidationException::withMessages($messages);
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

        Notification::make()
            ->title('Reservación creada correctamente')
            ->body('La reservación fue registrada exitosamente.')
            ->success()
            ->send();
    }
}

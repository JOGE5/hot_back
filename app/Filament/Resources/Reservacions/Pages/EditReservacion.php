<?php

namespace App\Filament\Resources\Reservacions\Pages;

use App\Filament\Resources\Reservacions\ReservacionResource;
use App\Http\Controllers\Admin\ReciboReservacionController;
use App\Models\Habitacion;
use App\Models\Reservacion;
use App\Support\LogSistema;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditReservacion extends EditRecord
{
    protected static string $resource = ReservacionResource::class;

    protected ?string $estadoPagoAnterior = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('descargar_recibo')
                ->label('Descargar recibo')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn (): string => route('admin.reservaciones.recibo', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->puedeGenerarRecibo($this->record)),

            Action::make('enviar_recibo')
                ->label('Enviar recibo')
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Enviar recibo por correo')
                ->modalDescription('Se enviará el recibo al correo registrado del huésped.')
                ->action(fn () => app(ReciboReservacionController::class)->enviarCorreo($this->record))
                ->visible(fn (): bool => $this->puedeEnviarRecibo($this->record)),

            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->estadoPagoAnterior = $this->record->estado_pago;

        unset($data['filtro_tipo_habitacion'], $data['filtro_estado_habitacion']);

        $habitacionId = $data['habitacion_id'] ?? null;
        $huespedId = $data['huesped_id'] ?? null;
        $fechaEntrada = $data['fecha_entrada'] ?? null;
        $fechaSalida = $data['fecha_salida'] ?? null;
        $cantidadPersonas = $data['cantidad_personas'] ?? null;

        if ($huespedId) {
            $tieneReservaActiva = Reservacion::query()
                ->where('huesped_id', $huespedId)
                ->where('id', '!=', $this->record->id)
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

            if (in_array($habitacion->estado, ['Mantenimiento', 'Inactiva'])) {
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

            $cruce = Reservacion::habitacionTieneConflictoDisponibilidad(
                $habitacionId,
                $fechaEntrada,
                $fechaSalida,
                $this->record->id,
            );

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

    protected function afterSave(): void
    {
        $this->record->loadMissing(['huesped', 'habitacion']);

        LogSistema::registrar(
            'EDITAR',
            'Reservaciones',
            'Reservación editada #' . $this->record->id . ' para huésped ' . ($this->record->huesped?->nombres ?? 'Sin huésped') . ' en habitación ' . ($this->record->habitacion?->numero ?? 'Sin habitación') . '.'
        );

        if ($this->estadoPagoAnterior !== 'Confirmado' && $this->record->estado_pago === 'Confirmado') {
            LogSistema::registrar(
                'CONFIRMAR_PAGO',
                'Pagos',
                'Pago confirmado desde reservación #' . $this->record->id . ' por Bs. ' . $this->record->total . '.'
            );
        }
    }

    private function puedeGenerarRecibo(Reservacion $reservacion): bool
    {
        return $this->puedeGestionarRecibos()
            && $reservacion->estado_pago === 'Confirmado'
            && $reservacion->total > 0
            && filled($reservacion->codigo_checkin);
    }

    private function puedeEnviarRecibo(Reservacion $reservacion): bool
    {
        $reservacion->loadMissing('huesped');

        return $this->puedeGenerarRecibo($reservacion)
            && filled($reservacion->huesped?->correo_electronico);
    }

    private function puedeGestionarRecibos(): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, [
            'SUPER ADMIN',
            'ADMIN',
            'RECEPCIONISTA',
        ], true);
    }
}

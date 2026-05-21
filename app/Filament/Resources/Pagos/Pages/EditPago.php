<?php

namespace App\Filament\Resources\Pagos\Pages;

use App\Filament\Resources\Pagos\PagoResource;
use App\Http\Controllers\Admin\ReciboReservacionController;
use App\Models\Reservacion;
use App\Support\LogSistema;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

class EditPago extends EditRecord
{
    protected static string $resource = PagoResource::class;

    protected ?string $estadoPagoAnterior = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('descargar_recibo')
                ->label('Descargar recibo')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn (): string => $this->record->reservacion
                    ? route('admin.reservaciones.recibo', $this->record->reservacion)
                    : '#')
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->puedeGenerarRecibo($this->record->reservacion)),

            Action::make('enviar_recibo')
                ->label('Enviar recibo')
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Enviar recibo por correo')
                ->modalDescription('Se enviará el recibo al correo registrado del huésped.')
                ->action(fn () => $this->record->reservacion
                    ? app(ReciboReservacionController::class)->enviarCorreo($this->record->reservacion)
                    : null)
                ->visible(fn (): bool => $this->puedeEnviarRecibo($this->record->reservacion)),

            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->estadoPagoAnterior = $this->record->estado_pago;
    }

    protected function afterSave(): void
    {
        if ($this->estadoPagoAnterior === 'Confirmado' || $this->record->estado_pago !== 'Confirmado') {
            return;
        }

        LogSistema::registrar(
            'CONFIRMAR_PAGO',
            'Pagos',
            'Pago confirmado #' . $this->record->id . ' para reservación #' . $this->record->reservacion_id . ' por Bs. ' . $this->record->monto . '.'
        );
    }

    private function puedeGenerarRecibo(?Reservacion $reservacion): bool
    {
        return $reservacion
            && $this->puedeGestionarRecibos()
            && $reservacion->estado_pago === 'Confirmado'
            && $reservacion->total > 0
            && filled($reservacion->codigo_checkin);
    }

    private function puedeEnviarRecibo(?Reservacion $reservacion): bool
    {
        if (! $reservacion) {
            return false;
        }

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

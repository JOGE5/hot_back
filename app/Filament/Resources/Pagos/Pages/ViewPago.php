<?php

namespace App\Filament\Resources\Pagos\Pages;

use App\Filament\Resources\Pagos\PagoResource;
use App\Http\Controllers\Admin\ReciboReservacionController;
use App\Models\Reservacion;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ViewRecord;

class ViewPago extends ViewRecord
{
    protected static string $resource = PagoResource::class;

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

            EditAction::make(),
        ];
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

<?php

namespace App\Filament\Resources\Reservacions\Pages;

use App\Filament\Resources\Reservacions\ReservacionResource;
use App\Http\Controllers\Admin\ReciboReservacionController;
use App\Models\Reservacion;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ViewRecord;

class ViewReservacion extends ViewRecord
{
    protected static string $resource = ReservacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reservacion_finalizada')
                ->label('Reservación finalizada. Solo disponible para consulta histórica.')
                ->color('gray')
                ->icon('heroicon-o-lock-closed')
                ->disabled()
                ->visible(fn (): bool => $this->record->estaCerradaPorCheckout()),

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

            EditAction::make()
                ->visible(fn (): bool => ReservacionResource::canEdit($this->record)),
        ];
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

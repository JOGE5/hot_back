<?php

namespace App\Filament\Resources\HuespedesEliminados\Pages;

use App\Filament\Resources\HuespedesEliminados\HuespedEliminadoResource;
use App\Mail\ReportePapeleraMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ListHuespedesEliminados extends ListRecords
{
    protected static string $resource = HuespedEliminadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('marcar_papelera_vaciada')
                ->label('Vaciar papelera')
                ->icon('heroicon-o-archive-box-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Vaciar papelera lógicamente')
                ->modalDescription('Esta acción marcará la papelera como vaciada lógicamente. Los registros no se borrarán físicamente.')
                ->modalSubmitActionLabel('Sí, vaciar papelera')
                ->visible(fn (): bool => $this->tieneRol(['SUPER ADMIN', 'ADMIN']))
                ->action(fn (): mixed => $this->vaciarPapeleraLogicamente()),

            Action::make('enviar_reporte_papelera')
                ->label('Enviar reporte de papelera')
                ->icon('heroicon-o-envelope')
                ->color('primary')
                ->visible(fn (): bool => $this->tieneRol(['SUPER ADMIN', 'ADMIN']))
                ->action(fn (): mixed => $this->enviarReportePapelera()),
        ];
    }

    private function vaciarPapeleraLogicamente(): void
    {
        $query = HuespedEliminadoResource::getEloquentQuery();

        if (! $query->exists()) {
            Notification::make()
                ->title('No hay registros pendientes para vaciar.')
                ->warning()
                ->send();

            return;
        }

        $query->update([
            'papelera_vaciada_at' => now(),
            'papelera_vaciada_por' => auth()->id(),
        ]);

        $this->resetTable();

        Notification::make()
            ->title('La papelera fue marcada como vaciada lógicamente.')
            ->success()
            ->send();
    }

    private function enviarReportePapelera(): void
    {
        $user = Filament::auth()->user();

        if (! $user?->email) {
            $this->notificarErrorReporte();

            return;
        }

        try {
            $registros = HuespedEliminadoResource::getEloquentQuery()
                ->orderByDesc('deleted_at')
                ->get();

            $pdfData = Pdf::loadView('reportes.reporte-papelera', [
                'modulo' => 'Huéspedes eliminados',
                'fechaGeneracion' => Carbon::now()->format('d/m/Y H:i'),
                'usuarioSolicitante' => $user->name . ' (' . $user->email . ')',
                'cantidad' => $registros->count(),
                'columnas' => ['ID', 'Nombre', 'Documento', 'Teléfono', 'Correo', 'Estado actual', 'Dado de baja en', 'Papelera vaciada en'],
                'registros' => $this->formatearRegistrosHuespedes($registros),
            ])->output();

            Mail::to($user->email)->send(new ReportePapeleraMail(
                modulo: 'Huéspedes eliminados',
                pdfData: $pdfData,
                nombreArchivo: 'reporte_papelera_huespedes_' . now()->format('Ymd_His') . '.pdf',
            ));

            Notification::make()
                ->title('Se envió el reporte de bajas lógicas a tu correo.')
                ->success()
                ->send();
        } catch (Throwable) {
            $this->notificarErrorReporte();
        }
    }

    private function formatearRegistrosHuespedes(Collection $registros): Collection
    {
        return $registros->map(fn ($huesped): array => [
            $huesped->id,
            trim($huesped->nombres . ' ' . $huesped->apellido_paterno . ' ' . ($huesped->apellido_materno ?? '')),
            trim($huesped->tipo_documento . ' ' . $huesped->numero_documento),
            $huesped->telefono ?? 'Sin teléfono',
            $huesped->correo_electronico ?? 'Sin correo',
            $huesped->estado ? 'Activo' : 'Inactivo',
            $huesped->deleted_at?->format('d/m/Y H:i') ?? 'Sin fecha',
            $huesped->papelera_vaciada_at?->format('d/m/Y H:i') ?? 'No marcada',
        ]);
    }

    private function notificarErrorReporte(): void
    {
        Notification::make()
            ->title('No se pudo enviar el reporte de bajas lógicas.')
            ->danger()
            ->send();
    }

    private function tieneRol(array $roles): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, $roles, true);
    }
}

<?php

namespace App\Filament\Resources\UsuariosEliminados\Pages;

use App\Filament\Resources\UsuariosEliminados\UsuarioEliminadoResource;
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

class ListUsuariosEliminados extends ListRecords
{
    protected static string $resource = UsuarioEliminadoResource::class;

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
        $query = UsuarioEliminadoResource::getEloquentQuery();

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
            $registros = UsuarioEliminadoResource::getEloquentQuery()
                ->with('role')
                ->orderByDesc('deleted_at')
                ->get();

            $pdfData = Pdf::loadView('reportes.reporte-papelera', [
                'modulo' => 'Usuarios eliminados',
                'fechaGeneracion' => Carbon::now()->format('d/m/Y H:i'),
                'usuarioSolicitante' => $user->name . ' (' . $user->email . ')',
                'cantidad' => $registros->count(),
                'columnas' => ['ID', 'Nombre', 'Correo', 'Rol', 'Estado actual', 'Dado de baja en', 'Papelera vaciada en'],
                'registros' => $this->formatearRegistrosUsuarios($registros),
            ])->output();

            Mail::to($user->email)->send(new ReportePapeleraMail(
                modulo: 'Usuarios eliminados',
                pdfData: $pdfData,
                nombreArchivo: 'reporte_papelera_usuarios_' . now()->format('Ymd_His') . '.pdf',
            ));

            Notification::make()
                ->title('Se envió el reporte de bajas lógicas a tu correo.')
                ->success()
                ->send();
        } catch (Throwable) {
            $this->notificarErrorReporte();
        }
    }

    private function formatearRegistrosUsuarios(Collection $registros): Collection
    {
        return $registros->map(fn ($usuario): array => [
            $usuario->id,
            $usuario->name,
            $usuario->email,
            $usuario->role?->nombre ?? 'Sin rol',
            $usuario->estado ? 'Activo' : 'Inactivo',
            $usuario->deleted_at?->format('d/m/Y H:i') ?? 'Sin fecha',
            $usuario->papelera_vaciada_at?->format('d/m/Y H:i') ?? 'No marcada',
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

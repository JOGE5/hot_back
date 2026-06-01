<?php

namespace App\Filament\Resources\Huespeds\Pages;

use App\Exports\ReporteDinamicoExport;
use App\Filament\Resources\Huespeds\HuespedResource;
use App\Filament\Resources\HuespedesEliminados\HuespedEliminadoResource;
use App\Support\Admin\ReporteDinamicoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListHuespeds extends ListRecords
{
    protected static string $resource = HuespedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reporte_dinamico_pdf')
                ->label('Reporte dinámico PDF')
                ->color('danger')
                ->icon('heroicon-o-document-text')
                ->form($this->formularioReporteDinamico('huespedes'))
                ->modalSubmitActionLabel('Generar PDF')
                ->visible(fn (): bool => $this->puedeGenerarReportesDinamicos())
                ->action(fn (array $data): mixed => $this->generarReporteDinamicoPdf('huespedes', 'Reporte dinámico de huéspedes', $data)),

            Action::make('reporte_dinamico_excel')
                ->label('Reporte dinámico Excel')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->form($this->formularioReporteDinamico('huespedes'))
                ->modalSubmitActionLabel('Generar Excel')
                ->visible(fn (): bool => $this->puedeGenerarReportesDinamicos())
                ->action(fn (array $data): mixed => $this->generarReporteDinamicoExcel('huespedes', 'reporte_dinamico_huespedes', $data)),

            Action::make('papelera_huespedes')
                ->label('Papelera de huéspedes')
                ->color('gray')
                ->icon('heroicon-o-trash')
                ->url(HuespedEliminadoResource::getUrl('index'))
                ->visible(function () {
                    $user = Filament::auth()->user();
                    return $user?->role && in_array($user->role->nombre, ['SUPER ADMIN', 'ADMIN']);
                }),

            CreateAction::make()
                ->label('Nuevo huésped')
                ->visible(fn (): bool => HuespedResource::canCreate()),
        ];
    }

    private function formularioReporteDinamico(string $modulo): array
    {
        return [
            CheckboxList::make('columnas')
                ->label('Columnas a incluir')
                ->options(app(ReporteDinamicoService::class)->columnasPermitidas($modulo))
                ->columns(2)
                ->bulkToggleable(),
        ];
    }

    private function generarReporteDinamicoPdf(string $modulo, string $titulo, array $data): mixed
    {
        $columnas = $this->columnasSeleccionadas($modulo, $data);

        if (empty($columnas)) {
            return $this->notificarColumnasRequeridas();
        }

        $registros = $this->getFilteredTableQuery()->get();
        $service = app(ReporteDinamicoService::class);
        $user = Filament::auth()->user();

        $pdf = Pdf::loadView('reportes.reporte-dinamico', [
            'titulo' => $titulo,
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
            'usuarioGenerador' => trim(($user?->name ?? 'Usuario') . ' (' . ($user?->email ?? 'sin correo') . ')'),
            'columnas' => $columnas,
            'filas' => $service->filas($modulo, $registros, $columnas),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $this->nombreArchivo($modulo, 'pdf')
        );
    }

    private function generarReporteDinamicoExcel(string $modulo, string $nombreBase, array $data): mixed
    {
        $columnas = $this->columnasSeleccionadas($modulo, $data);

        if (empty($columnas)) {
            return $this->notificarColumnasRequeridas();
        }

        return Excel::download(
            new ReporteDinamicoExport($modulo, $this->getFilteredTableQuery()->get(), $columnas),
            $nombreBase . '_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    private function columnasSeleccionadas(string $modulo, array $data): array
    {
        return app(ReporteDinamicoService::class)
            ->columnasSeleccionadas($modulo, $data['columnas'] ?? []);
    }

    private function notificarColumnasRequeridas(): null
    {
        Notification::make()
            ->title('Selecciona al menos una columna para generar el reporte.')
            ->warning()
            ->send();

        return null;
    }

    private function nombreArchivo(string $modulo, string $extension): string
    {
        return 'reporte_dinamico_' . $modulo . '_' . now()->format('Ymd_His') . '.' . $extension;
    }

    private function puedeGenerarReportesDinamicos(): bool
    {
        $user = Filament::auth()->user();

        return $user?->role && in_array($user->role->nombre, ['SUPER ADMIN', 'ADMIN'], true);
    }
}

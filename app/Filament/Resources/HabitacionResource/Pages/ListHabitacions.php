<?php

namespace App\Filament\Resources\HabitacionResource\Pages;

use App\Exports\ReporteDinamicoExport;
use App\Filament\Resources\HabitacionResource;
use App\Models\Habitacion;
use App\Support\Admin\ReporteDinamicoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ListHabitacions extends Page
{
    protected static string $resource = HabitacionResource::class;

    protected string $view = 'filament.resources.habitacion-resource.pages.list-habitaciones';

    protected static ?string $title = 'Habitaciones';
    
    protected ?string $heading = 'Habitaciones';

    public ?string $buscar = null;
    public ?string $estado = null;
    public ?string $tipo = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reporte_dinamico_pdf')
                ->label('Reporte dinámico PDF')
                ->color('danger')
                ->icon('heroicon-o-document-text')
                ->form($this->formularioReporteDinamico('habitaciones'))
                ->modalSubmitActionLabel('Generar PDF')
                ->visible(fn (): bool => $this->puedeGenerarReportesDinamicos())
                ->action(fn (array $data): mixed => $this->generarReporteDinamicoPdf('habitaciones', 'Reporte dinámico de habitaciones', $data)),

            Action::make('reporte_dinamico_excel')
                ->label('Reporte dinámico Excel')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->form($this->formularioReporteDinamico('habitaciones'))
                ->modalSubmitActionLabel('Generar Excel')
                ->visible(fn (): bool => $this->puedeGenerarReportesDinamicos())
                ->action(fn (array $data): mixed => $this->generarReporteDinamicoExcel('habitaciones', 'reporte_dinamico_habitaciones', $data)),

            CreateAction::make()
                ->label('Nueva habitación')
                ->visible(fn (): bool => HabitacionResource::canCreate()),
        ];
    }

    public function getHabitacionesProperty(): Collection
    {
        return $this->habitacionesReporteQuery()
            ->orderBy('numero')
            ->get();
    }

    private function habitacionesReporteQuery(): Builder
    {
        return Habitacion::query()
            ->when(! empty($this->buscar), fn (Builder $query): Builder => $query->where('numero', 'like', '%' . $this->buscar . '%'))
            ->when(! empty($this->estado), fn (Builder $query): Builder => $query->where('estado', $this->estado))
            ->when(! empty($this->tipo), fn (Builder $query): Builder => $query->where('tipo', $this->tipo));
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

        $registros = $this->habitacionesReporteQuery()->orderBy('numero')->get();
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
            new ReporteDinamicoExport($modulo, $this->habitacionesReporteQuery()->orderBy('numero')->get(), $columnas),
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

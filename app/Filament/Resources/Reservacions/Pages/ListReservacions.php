<?php

namespace App\Filament\Resources\Reservacions\Pages;

use App\Exports\ReporteDinamicoExport;
use App\Filament\Resources\Reservacions\ReservacionResource;
use App\Models\Habitacion;
use App\Models\Huesped;
use App\Models\Reservacion;
use App\Support\Admin\ReporteDinamicoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ListReservacions extends Page
{
    protected static string $resource = ReservacionResource::class;

    protected static ?string $title = 'Reservaciones';

    protected string $view = 'filament.resources.reservacions.pages.list-reservaciones';

    public ?string $buscar = null;
    public ?string $estado_reservacion = null;
    public ?string $estado_pago = null;
    public ?string $origen_reservacion = null;
    public ?string $metodo_pago = null;
    public ?string $fecha_entrada_desde = null;
    public ?string $fecha_entrada_hasta = null;
    public ?string $fecha_salida_desde = null;
    public ?string $fecha_salida_hasta = null;
    public ?string $total_desde = null;
    public ?string $total_hasta = null;
    public ?string $huesped_id = null;
    public ?string $habitacion_id = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reporte_dinamico_pdf')
                ->label('Reporte dinámico PDF')
                ->color('danger')
                ->icon('heroicon-o-document-text')
                ->form($this->formularioReporteDinamico('reservaciones'))
                ->modalSubmitActionLabel('Generar PDF')
                ->visible(fn (): bool => $this->puedeGenerarReportesDinamicos())
                ->action(fn (array $data): mixed => $this->generarReporteDinamicoPdf('reservaciones', 'Reporte dinámico de reservaciones', $data)),

            Action::make('reporte_dinamico_excel')
                ->label('Reporte dinámico Excel')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->form($this->formularioReporteDinamico('reservaciones'))
                ->modalSubmitActionLabel('Generar Excel')
                ->visible(fn (): bool => $this->puedeGenerarReportesDinamicos())
                ->action(fn (array $data): mixed => $this->generarReporteDinamicoExcel('reservaciones', 'reporte_dinamico_reservaciones', $data)),
        ];
    }

    public function getReservacionesProperty()
    {
        return $this->reservacionesReporteQuery()
            ->orderBy('fecha_entrada', 'desc')
            ->paginate(12);
    }

    public function toggleOrigen(string $origen): void
    {
        $this->origen_reservacion = $this->origen_reservacion === $origen
            ? null
            : $origen;
    }

    public function getHuespedesFiltroProperty()
    {
        return Huesped::withTrashed()
            ->orderBy('nombres')
            ->get(['id', 'nombres', 'apellido_paterno', 'apellido_materno', 'numero_documento']);
    }

    public function getHabitacionesFiltroProperty()
    {
        return Habitacion::withTrashed()
            ->orderBy('numero')
            ->get(['id', 'numero', 'tipo']);
    }

    private function reservacionesReporteQuery(): Builder
    {
        return Reservacion::query()
            ->with([
                'huesped' => fn ($query) => $query->withTrashed(),
                'habitacion' => fn ($query) => $query->withTrashed(),
            ])
            ->when($this->buscar, function (Builder $query, $buscar): Builder {
                return $query->where(function (Builder $query) use ($buscar): void {
                    $query->where('codigo_checkin', 'like', "%{$buscar}%")
                    ->orWhere('fecha_entrada', 'like', "%{$buscar}%")
                    ->orWhere('fecha_salida', 'like', "%{$buscar}%")
                    ->orWhere('cantidad_personas', 'like', "%{$buscar}%")
                    ->orWhere('total', 'like', "%{$buscar}%")
                    ->orWhere('estado_reservacion', 'like', "%{$buscar}%")
                    ->orWhere('estado_pago', 'like', "%{$buscar}%")
                    ->orWhere('metodo_pago', 'like', "%{$buscar}%")
                    ->orWhere('origen_reservacion', 'like', "%{$buscar}%")
                    ->orWhereHas('huesped', function (Builder $q) use ($buscar): void {
                        $q->withTrashed()
                            ->where('nombres', 'like', "%{$buscar}%")
                            ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                            ->orWhere('apellido_materno', 'like', "%{$buscar}%")
                            ->orWhere('numero_documento', 'like', "%{$buscar}%")
                            ->orWhere('correo_electronico', 'like', "%{$buscar}%")
                            ->orWhere('telefono', 'like', "%{$buscar}%");
                    })->orWhereHas('habitacion', function (Builder $q) use ($buscar): void {
                        $q->withTrashed()->where('numero', 'like', "%{$buscar}%");
                    });
                });
            })
            ->when($this->estado_reservacion, fn (Builder $query, string $estado): Builder => $query->where('estado_reservacion', $estado))
            ->when($this->estado_pago, fn (Builder $query, string $estado): Builder => $query->where('estado_pago', $estado))
            ->when($this->origen_reservacion, fn (Builder $query, string $origen): Builder => $query->where('origen_reservacion', $origen))
            ->when($this->metodo_pago, fn (Builder $query, string $metodo): Builder => $query->where('metodo_pago', $metodo))
            ->when($this->huesped_id, fn (Builder $query, string $huespedId): Builder => $query->where('huesped_id', $huespedId))
            ->when($this->habitacion_id, fn (Builder $query, string $habitacionId): Builder => $query->where('habitacion_id', $habitacionId))
            ->when($this->fecha_entrada_desde, fn (Builder $query, string $fecha): Builder => $query->whereDate('fecha_entrada', '>=', $fecha))
            ->when($this->fecha_entrada_hasta, fn (Builder $query, string $fecha): Builder => $query->whereDate('fecha_entrada', '<=', $fecha))
            ->when($this->fecha_salida_desde, fn (Builder $query, string $fecha): Builder => $query->whereDate('fecha_salida', '>=', $fecha))
            ->when($this->fecha_salida_hasta, fn (Builder $query, string $fecha): Builder => $query->whereDate('fecha_salida', '<=', $fecha))
            ->when(filled($this->total_desde), fn (Builder $query): Builder => $query->where('total', '>=', $this->total_desde))
            ->when(filled($this->total_hasta), fn (Builder $query): Builder => $query->where('total', '<=', $this->total_hasta));
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

        $registros = $this->reservacionesReporteQuery()
            ->orderBy('fecha_entrada', 'desc')
            ->get();
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

        $registros = $this->reservacionesReporteQuery()
            ->orderBy('fecha_entrada', 'desc')
            ->get();

        return Excel::download(
            new ReporteDinamicoExport($modulo, $registros, $columnas),
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

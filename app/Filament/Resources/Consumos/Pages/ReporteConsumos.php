<?php

namespace App\Filament\Resources\Consumos\Pages;

use App\Exports\ConsumosExport;
use App\Filament\Resources\Consumos\ConsumoResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteConsumos extends Page
{
    protected static string $resource = ConsumoResource::class;

    protected static ?string $title = 'Reporte de consumos';

    protected string $view = 'filament.resources.consumos.pages.reporte-consumos';

    public ?string $fecha_desde = null;

    public ?string $fecha_hasta = null;

    public ?string $plato_id = null;

    public ?string $huesped_id = null;

    public ?string $habitacion_id = null;

    public ?string $estado = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('volver_consumos')
                ->label('Volver a consumos')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(ConsumoResource::getUrl('index')),
        ];
    }

    public function limpiarFiltros(): void
    {
        $this->reset([
            'fecha_desde',
            'fecha_hasta',
            'plato_id',
            'huesped_id',
            'habitacion_id',
            'estado',
        ]);
    }

    public function exportarPdf(): mixed
    {
        $consumos = $this->consumosReporte();
        $pdf = Pdf::loadView('reportes.consumos_pdf', [
            'consumos' => $consumos,
            'filtros' => $this->filtrosAplicados(),
            'totalGeneralConfirmado' => $this->totalGeneralConfirmado($consumos),
            'fechaGeneracion' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'reporte_consumos_' . now()->format('Ymd_His') . '.pdf'
        );
    }

    public function exportarExcel(): mixed
    {
        $consumos = $this->consumosReporte();

        return Excel::download(
            new ConsumosExport($consumos, $this->totalGeneralConfirmado($consumos)),
            'reporte_consumos_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function getConsumosProperty(): Collection
    {
        return $this->consumosReporte();
    }

    public function getTotalGeneralConfirmadoProperty(): float
    {
        return $this->totalGeneralConfirmado($this->consumosReporte());
    }

    public function getPlatosFiltroProperty(): Collection
    {
        return DB::table('platos')
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
    }

    public function getHuespedesFiltroProperty(): Collection
    {
        return DB::table('huespedes')
            ->orderBy('nombres')
            ->get(['id', 'nombres', 'apellido_paterno', 'apellido_materno', 'numero_documento']);
    }

    public function getHabitacionesFiltroProperty(): Collection
    {
        return DB::table('habitaciones')
            ->orderBy('numero')
            ->get(['id', 'numero', 'tipo']);
    }

    private function consumosReporte(): Collection
    {
        return DB::table('consumos')
            ->leftJoin('huespedes', 'huespedes.id', '=', 'consumos.huesped_id')
            ->leftJoin('habitaciones', 'habitaciones.id', '=', 'consumos.habitacion_id')
            ->leftJoin('platos', 'platos.id', '=', 'consumos.plato_id')
            ->leftJoin('users', 'users.id', '=', 'consumos.registrado_por')
            ->select([
                'consumos.id',
                'consumos.fecha_consumo',
                'consumos.cantidad',
                'consumos.precio_unitario',
                'consumos.total',
                'consumos.estado',
                'consumos.observacion',
                DB::raw("COALESCE(NULLIF(TRIM(CONCAT_WS(' ', huespedes.nombres, huespedes.apellido_paterno, huespedes.apellido_materno)), ''), 'Huésped no disponible') as huesped_nombre"),
                DB::raw("COALESCE(CONCAT('Habitación ', habitaciones.numero), 'Habitación no disponible') as habitacion_texto"),
                DB::raw("COALESCE(platos.nombre, 'Plato no disponible') as plato_nombre"),
                DB::raw("COALESCE(NULLIF(TRIM(CONCAT_WS(' ', users.nombres, users.apellido_paterno, users.apellido_materno)), ''), users.name, 'Usuario no disponible') as registrado_por_nombre"),
            ])
            ->whereNull('consumos.deleted_at')
            ->when(
                filled($this->fecha_desde),
                fn ($query) => $query->whereDate('consumos.fecha_consumo', '>=', $this->fecha_desde),
            )
            ->when(
                filled($this->fecha_hasta),
                fn ($query) => $query->whereDate('consumos.fecha_consumo', '<=', $this->fecha_hasta),
            )
            ->when(
                filled($this->plato_id),
                fn ($query) => $query->where('consumos.plato_id', $this->plato_id),
            )
            ->when(
                filled($this->huesped_id),
                fn ($query) => $query->where('consumos.huesped_id', $this->huesped_id),
            )
            ->when(
                filled($this->habitacion_id),
                fn ($query) => $query->where('consumos.habitacion_id', $this->habitacion_id),
            )
            ->when(
                filled($this->estado),
                fn ($query) => $query->where('consumos.estado', $this->estado),
            )
            ->orderByDesc('consumos.fecha_consumo')
            ->orderByDesc('consumos.id')
            ->get();
    }

    private function totalGeneralConfirmado(Collection $consumos): float
    {
        return (float) $consumos
            ->where('estado', 'Confirmado')
            ->sum('total');
    }

    private function filtrosAplicados(): array
    {
        return [
            'Fecha desde' => $this->fecha_desde ?: 'Todos',
            'Fecha hasta' => $this->fecha_hasta ?: 'Todos',
            'Plato' => $this->nombreFiltro('platos', $this->plato_id, 'nombre', 'Todos'),
            'Huésped' => $this->nombreHuespedFiltro(),
            'Habitación' => $this->nombreHabitacionFiltro(),
            'Estado' => $this->estado ?: 'Todos',
        ];
    }

    private function nombreFiltro(string $tabla, ?string $id, string $columna, string $porDefecto): string
    {
        if (! filled($id)) {
            return $porDefecto;
        }

        return DB::table($tabla)->where('id', $id)->value($columna) ?? $porDefecto;
    }

    private function nombreHuespedFiltro(): string
    {
        if (! filled($this->huesped_id)) {
            return 'Todos';
        }

        $huesped = DB::table('huespedes')
            ->where('id', $this->huesped_id)
            ->first(['nombres', 'apellido_paterno', 'apellido_materno', 'numero_documento']);

        if (! $huesped) {
            return 'Huésped no disponible';
        }

        $nombre = trim(implode(' ', array_filter([
            $huesped->nombres,
            $huesped->apellido_paterno,
            $huesped->apellido_materno,
        ])));

        return trim($nombre . ($huesped->numero_documento ? ' - ' . $huesped->numero_documento : ''));
    }

    private function nombreHabitacionFiltro(): string
    {
        if (! filled($this->habitacion_id)) {
            return 'Todos';
        }

        $habitacion = DB::table('habitaciones')
            ->where('id', $this->habitacion_id)
            ->first(['numero', 'tipo']);

        if (! $habitacion) {
            return 'Habitación no disponible';
        }

        return 'Habitación ' . $habitacion->numero . ($habitacion->tipo ? ' - ' . $habitacion->tipo : '');
    }
}

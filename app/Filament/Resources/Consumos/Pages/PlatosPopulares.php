<?php

namespace App\Filament\Resources\Consumos\Pages;

use App\Filament\Resources\Consumos\ConsumoResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlatosPopulares extends Page
{
    protected static string $resource = ConsumoResource::class;

    protected static ?string $title = 'Platos populares';

    protected string $view = 'filament.resources.consumos.pages.platos-populares';

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

    public function getPlatosPopularesProperty(): Collection
    {
        return DB::table('consumos')
            ->leftJoin('platos', 'platos.id', '=', 'consumos.plato_id')
            ->select([
                'consumos.plato_id',
                DB::raw("COALESCE(platos.nombre, 'Plato no disponible') as plato_nombre"),
                DB::raw('SUM(consumos.cantidad) as cantidad_total_vendida'),
                DB::raw('SUM(consumos.total) as total_generado'),
                DB::raw('COUNT(consumos.id) as numero_consumos'),
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
                fn ($query) => $query->where('consumos.estado', 'Confirmado'),
            )
            ->groupBy('consumos.plato_id', 'platos.nombre')
            ->orderByDesc('cantidad_total_vendida')
            ->get();
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
}

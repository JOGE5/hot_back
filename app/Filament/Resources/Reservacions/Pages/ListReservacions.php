<?php

namespace App\Filament\Resources\Reservacions\Pages;

use App\Filament\Resources\Reservacions\ReservacionResource;
use App\Models\Reservacion;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;

class ListReservacions extends Page
{
    protected static string $resource = ReservacionResource::class;

    protected static ?string $title = 'Reservaciones';

    protected string $view = 'filament.resources.reservacions.pages.list-reservaciones';

    public ?string $buscar = null;
    public ?string $estado_reservacion = null;
    public ?string $estado_pago = null;
    public ?string $origen_reservacion = null;

    public function getReservacionesProperty()
    {
        return Reservacion::query()
            ->with(['huesped', 'habitacion'])
            ->when($this->buscar, function (Builder $query, $buscar) {
                $query->whereHas('huesped', function (Builder $q) use ($buscar) {
                    $q->where('nombres', 'like', "%{$buscar}%")
                        ->orWhere('apellido_paterno', 'like', "%{$buscar}%")
                        ->orWhere('apellido_materno', 'like', "%{$buscar}%")
                        ->orWhere('numero_documento', 'like', "%{$buscar}%");
                })->orWhereHas('habitacion', function (Builder $q) use ($buscar) {
                    $q->where('numero', 'like', "%{$buscar}%");
                });
            })
            ->when($this->estado_reservacion, function (Builder $query, $estado) {
                $query->where('estado_reservacion', $estado);
            })
            ->when($this->estado_pago, function (Builder $query, $estado) {
                $query->where('estado_pago', $estado);
            })
            ->when($this->origen_reservacion, function (Builder $query, $origen) {
                $query->where('origen_reservacion', $origen);
            })
            ->orderBy('fecha_entrada', 'desc')
            ->paginate(12);
    }

    public function toggleOrigen(string $origen): void
    {
        $this->origen_reservacion = $this->origen_reservacion === $origen
            ? null
            : $origen;
    }
}

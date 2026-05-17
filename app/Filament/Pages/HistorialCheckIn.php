<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Reservacion;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;

class HistorialCheckIn extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected string $view = 'filament.pages.historial-check-in';

    protected static ?string $navigationLabel = 'Historial de check';

    protected static string|UnitEnum|null $navigationGroup = 'Hotel';

    protected static ?string $title = 'Historial de Check';

    public $buscar = '';
    public $estado_reservacion = '';
    public $metodo_pago = '';
    public $fecha_desde = '';
    public $fecha_hasta = '';
    public $fecha_checkout_desde = '';
    public $fecha_checkout_hasta = '';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();
        return $user?->role && in_array($user->role->nombre, [
            'SUPER ADMIN',
            'ADMIN',
            'RECEPCIONISTA',
        ], true);
    }

    public function getReservacionesProperty()
    {
        $query = Reservacion::query()
            ->with(['huesped', 'habitacion', 'checkinUser', 'checkoutUser'])
            ->whereNotNull('checkin_at')
            ->whereIn('estado_reservacion', ['En estadía', 'Finalizada']);

        if (!empty($this->buscar)) {
            $busqueda = '%' . $this->buscar . '%';
            $query->where(function (Builder $q) use ($busqueda) {
                $q->where('codigo_checkin', 'like', $busqueda)
                  ->orWhere('codigo_checkout', 'like', $busqueda)
                  ->orWhereHas('huesped', function (Builder $h) use ($busqueda) {
                      $h->where('nombres', 'like', $busqueda)
                        ->orWhere('apellido_paterno', 'like', $busqueda)
                        ->orWhere('apellido_materno', 'like', $busqueda)
                        ->orWhere('numero_documento', 'like', $busqueda);
                  })
                  ->orWhereHas('habitacion', function (Builder $hab) use ($busqueda) {
                      $hab->where('numero', 'like', $busqueda);
                  });
            });
        }

        if (!empty($this->estado_reservacion)) {
            $query->where('estado_reservacion', $this->estado_reservacion);
        }

        if (!empty($this->metodo_pago)) {
            $query->where('metodo_pago', $this->metodo_pago);
        }

        if (!empty($this->fecha_desde)) {
            $query->whereDate('checkin_at', '>=', $this->fecha_desde);
        }

        if (!empty($this->fecha_hasta)) {
            $query->whereDate('checkin_at', '<=', $this->fecha_hasta);
        }

        if (!empty($this->fecha_checkout_desde)) {
            $query->whereDate('checkout_at', '>=', $this->fecha_checkout_desde);
        }

        if (!empty($this->fecha_checkout_hasta)) {
            $query->whereDate('checkout_at', '<=', $this->fecha_checkout_hasta);
        }

        return $query->orderBy('checkin_at', 'desc')->get();
    }

    public function exportarExcel()
    {
        return redirect()->route('admin.reportes.check-in.excel', [
            'buscar' => $this->buscar,
            'estado_reservacion' => $this->estado_reservacion,
            'metodo_pago' => $this->metodo_pago,
            'fecha_desde' => $this->fecha_desde,
            'fecha_hasta' => $this->fecha_hasta,
            'fecha_checkout_desde' => $this->fecha_checkout_desde,
            'fecha_checkout_hasta' => $this->fecha_checkout_hasta,
        ]);
    }

    public function exportarPdf()
    {
        return redirect()->route('admin.reportes.check-in.pdf', [
            'buscar' => $this->buscar,
            'estado_reservacion' => $this->estado_reservacion,
            'metodo_pago' => $this->metodo_pago,
            'fecha_desde' => $this->fecha_desde,
            'fecha_hasta' => $this->fecha_hasta,
            'fecha_checkout_desde' => $this->fecha_checkout_desde,
            'fecha_checkout_hasta' => $this->fecha_checkout_hasta,
        ]);
    }
}

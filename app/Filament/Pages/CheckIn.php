<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Reservacion;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use App\Support\LogSistema;
use BackedEnum;
use UnitEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CheckIn extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected string $view = 'filament.pages.check-in';

    protected static ?string $navigationLabel = 'Check';

    protected static string|UnitEnum|null $navigationGroup = 'Hotel';

    protected static ?string $title = 'Gestión de Check';

    // Propiedades Check-in
    public $codigo_checkin = '';
    public ?Reservacion $reservacion = null;
    public ?string $error_message = null;

    // Propiedades Check-out
    public $busqueda_checkout = '';
    public ?Reservacion $reservacion_checkout = null;
    public ?string $error_checkout = null;

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();
        return $user?->role && in_array($user->role->nombre, [
            'SUPER ADMIN',
            'ADMIN',
            'RECEPCIONISTA',
        ], true);
    }

    public function validarCodigo()
    {
        $this->error_message = null;
        $this->reservacion = null;

        if (empty($this->codigo_checkin)) {
            $this->error_message = 'Ingrese un código de check-in.';
            return;
        }

        $reserva = Reservacion::with(['huesped', 'habitacion'])->where('codigo_checkin', $this->codigo_checkin)->first();

        if (!$reserva) {
            $this->error_message = 'Código de check-in inválido o no disponible.';
            return;
        }

        if ($reserva->estado_pago !== 'Confirmado') {
            $this->error_message = 'La reservación no tiene pago confirmado.';
            return;
        }

        if ($reserva->estado_reservacion === 'Cancelada') {
            $this->error_message = 'Esta reservación fue cancelada.';
            return;
        }

        if ($reserva->estado_reservacion === 'Finalizada') {
            $this->error_message = 'Esta reservación ya fue finalizada.';
            return;
        }

        if ($reserva->estado_reservacion === 'En estadía' || !empty($reserva->checkin_at)) {
            $this->error_message = 'El check-in ya fue realizado.';
            return;
        }

        $today = today()->toDateString();
        $fechaEntrada = \Carbon\Carbon::parse($reserva->fecha_entrada)->toDateString();
        $fechaSalida = \Carbon\Carbon::parse($reserva->fecha_salida)->toDateString();

        if ($today < $fechaEntrada) {
            $this->error_message = 'El check-in solo puede realizarse desde la fecha de entrada.';
            return;
        }

        if ($today > $fechaSalida) {
            $this->error_message = 'No se puede realizar check-in porque la fecha de salida ya pasó.';
            return;
        }

        if ($reserva->estado_reservacion === 'Confirmada') {
            $this->reservacion = $reserva;
        } else {
            $this->error_message = 'Estado de reservación inválido para check-in.';
        }
    }

    public function confirmarCheckIn()
    {
        if ($this->reservacion && $this->reservacion->estado_reservacion === 'Confirmada') {
            $this->reservacion->estado_reservacion = 'En estadía';
            $this->reservacion->checkin_at = now();
            $this->reservacion->checkin_user_id = Filament::auth()->id();
            
            if (empty($this->reservacion->codigo_checkout)) {
                $this->reservacion->codigo_checkout = 'OUT-' . strtoupper(Str::random(6));
            }
            
            $this->reservacion->save();

            LogSistema::registrar(
                'CHECK_IN',
                'Check-in',
                'Check-in confirmado para reservación #' . $this->reservacion->id . ' con código ' . $this->reservacion->codigo_checkin . '.'
            );

            Notification::make()
                ->title('Check-in realizado correctamente.')
                ->success()
                ->send();

            $this->codigo_checkin = '';
            $this->reservacion = null;
            $this->error_message = null;
        }
    }

    public function buscarCheckOut()
    {
        $this->error_checkout = null;
        $this->reservacion_checkout = null;

        if (empty($this->busqueda_checkout)) {
            $this->error_checkout = 'Ingrese un término de búsqueda para check-out.';
            return;
        }

        $busqueda = $this->busqueda_checkout;

        $reserva = Reservacion::with(['huesped', 'habitacion'])
            ->where('estado_reservacion', 'En estadía')
            ->whereNotNull('checkin_at')
            ->whereNotNull('codigo_checkout')
            ->where(function (Builder $q) use ($busqueda) {
                $q->where('codigo_checkout', $busqueda)
                  ->orWhere('codigo_checkin', $busqueda)
                  ->orWhereHas('huesped', function (Builder $h) use ($busqueda) {
                      $h->where('numero_documento', $busqueda)
                        ->orWhere('nombres', 'like', '%' . $busqueda . '%')
                        ->orWhere('apellido_paterno', 'like', '%' . $busqueda . '%');
                  })
                  ->orWhereHas('habitacion', function (Builder $hab) use ($busqueda) {
                      $hab->where('numero', $busqueda);
                  });
            })
            ->first();

        if (!$reserva) {
            $this->error_checkout = 'No se encontró ninguna estadía activa coincidente.';
            return;
        }

        if (!empty($reserva->checkout_at)) {
            $this->error_checkout = 'El check-out ya fue realizado.';
            return;
        }

        $today = today()->toDateString();
        $fechaSalida = \Carbon\Carbon::parse($reserva->fecha_salida)->toDateString();

        if ($today < $fechaSalida) {
            $this->error_checkout = 'El check-out solo puede realizarse desde la fecha de salida.';
            return;
        }

        $this->reservacion_checkout = $reserva;
    }

    public function confirmarCheckOut()
    {
        if (!$this->reservacion_checkout) {
            $this->error_checkout = 'No hay reservación seleccionada.';
            return;
        }

        if ($this->reservacion_checkout->estado_reservacion !== 'En estadía' || 
            empty($this->reservacion_checkout->checkin_at) || 
            empty($this->reservacion_checkout->codigo_checkout)) {
            $this->error_checkout = 'No se puede realizar check-out porque esta reservación no tiene check-in confirmado.';
            return;
        }

        if (!empty($this->reservacion_checkout->checkout_at)) {
            $this->error_checkout = 'El check-out ya fue realizado.';
            return;
        }

        $today = today()->toDateString();
        $fechaSalida = \Carbon\Carbon::parse($this->reservacion_checkout->fecha_salida)->toDateString();

        if ($today < $fechaSalida) {
            $this->error_checkout = 'El check-out solo puede realizarse desde la fecha de salida.';
            return;
        }

        $this->reservacion_checkout->estado_reservacion = 'Finalizada';
        $this->reservacion_checkout->checkout_at = now();
        $this->reservacion_checkout->checkout_user_id = Filament::auth()->id();
        $this->reservacion_checkout->save();

        LogSistema::registrar(
            'CHECK_OUT',
            'Check-out',
            'Check-out confirmado para reservación #' . $this->reservacion_checkout->id . ' con código ' . $this->reservacion_checkout->codigo_checkout . '.'
        );

        Notification::make()
            ->title('Check-out realizado correctamente.')
            ->success()
            ->send();

        $this->busqueda_checkout = '';
        $this->reservacion_checkout = null;
        $this->error_checkout = null;
    }
}

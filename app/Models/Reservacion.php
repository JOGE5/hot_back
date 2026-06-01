<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Reservacion extends Model
{
    public const MARGEN_DISPONIBILIDAD_DIAS = 2;

    public const MENSAJE_HABITACION_NO_DISPONIBLE = 'La habitación no está disponible para esas fechas. Debe existir un margen mínimo de 2 días después del check-out anterior.';

    public const ESTADOS_BLOQUEANTES_DISPONIBILIDAD = [
        'Pendiente de pago',
        'Confirmada',
        'En estadía',
    ];

    protected $table = 'reservaciones';

    protected $fillable = [
        'huesped_id',
        'habitacion_id',
        'origen_reservacion',
        'fecha_entrada',
        'fecha_salida',
        'cantidad_personas',
        'total',
        'estado_reservacion',
        'metodo_pago',
        'estado_pago',
        'codigo_checkin',
        'observacion',
        'checkin_at',
        'checkin_user_id',
        'codigo_checkout',
        'checkout_at',
        'checkout_user_id',
    ];

    protected $casts = [
        'fecha_entrada' => 'date',
        'fecha_salida' => 'date',
        'cantidad_personas' => 'integer',
        'total' => 'decimal:2',
        'checkin_at' => 'datetime',
        'checkout_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(function (Reservacion $reservacion): void {
            if ($reservacion->estado_pago !== 'Confirmado') {
                return;
            }

            if (empty($reservacion->metodo_pago)) {
                return;
            }

            if ((float) $reservacion->total <= 0) {
                return;
            }

            $existePagoConfirmado = Pago::where('reservacion_id', $reservacion->id)
                ->where('estado_pago', 'Confirmado')
                ->exists();

            if ($existePagoConfirmado) {
                return;
            }

            Pago::create([
                'reservacion_id' => $reservacion->id,
                'monto' => $reservacion->total,
                'metodo_pago' => $reservacion->metodo_pago,
                'estado_pago' => 'Confirmado',
                'fecha_pago' => now(),
                'registrado_por' => Auth::id(),
                'observacion' => 'Pago registrado automáticamente desde reservación',
            ]);
        });
    }

    public static function estadosBloqueantesDisponibilidad(): array
    {
        return self::ESTADOS_BLOQUEANTES_DISPONIBILIDAD;
    }

    public function scopeBloqueantesDisponibilidad(Builder $query): Builder
    {
        return $query->whereIn('estado_reservacion', self::estadosBloqueantesDisponibilidad());
    }

    public function scopeConConflictoDisponibilidad(
        Builder $query,
        string $fechaEntrada,
        string $fechaSalida,
        int|string|null $ignorarReservacionId = null
    ): Builder {
        $entrada = Carbon::parse($fechaEntrada)->toDateString();
        $salidaConMargen = Carbon::parse($fechaSalida)
            ->addDays(self::MARGEN_DISPONIBILIDAD_DIAS)
            ->toDateString();

        return $query
            ->bloqueantesDisponibilidad()
            ->when($ignorarReservacionId, fn (Builder $query) => $query->where('id', '!=', $ignorarReservacionId))
            ->where('fecha_entrada', '<', $salidaConMargen)
            ->whereRaw('DATE_ADD(fecha_salida, INTERVAL ' . self::MARGEN_DISPONIBILIDAD_DIAS . ' DAY) > ?', [$entrada]);
    }

    public static function habitacionTieneConflictoDisponibilidad(
        int|string $habitacionId,
        string $fechaEntrada,
        string $fechaSalida,
        int|string|null $ignorarReservacionId = null
    ): bool {
        return self::query()
            ->where('habitacion_id', $habitacionId)
            ->conConflictoDisponibilidad($fechaEntrada, $fechaSalida, $ignorarReservacionId)
            ->exists();
    }

    public function estaCerradaPorCheckout(): bool
    {
        return trim((string) $this->estado_reservacion) === 'Finalizada'
            && $this->checkout_at !== null;
    }

    public function huesped(): BelongsTo
    {
        return $this->belongsTo(Huesped::class, 'huesped_id');
    }

    public function habitacion(): BelongsTo
    {
        return $this->belongsTo(Habitacion::class, 'habitacion_id');
    }

    public function checkinUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checkin_user_id');
    }

    public function checkoutUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checkout_user_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'reservacion_id');
    }
}

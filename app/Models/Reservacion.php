<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Reservacion extends Model
{
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

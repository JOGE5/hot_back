<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
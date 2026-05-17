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
    ];

    protected $casts = [
        'fecha_entrada' => 'date',
        'fecha_salida' => 'date',
        'cantidad_personas' => 'integer',
        'total' => 'decimal:2',
    ];

    public function huesped(): BelongsTo
    {
        return $this->belongsTo(Huesped::class, 'huesped_id');
    }

    public function habitacion(): BelongsTo
    {
        return $this->belongsTo(Habitacion::class, 'habitacion_id');
    }
}
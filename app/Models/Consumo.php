<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Consumo extends Model
{
    use SoftDeletes;

    protected $table = 'consumos';

    protected $fillable = [
        'reservacion_id',
        'huesped_id',
        'habitacion_id',
        'plato_id',
        'cantidad',
        'precio_unitario',
        'total',
        'fecha_consumo',
        'estado',
        'observacion',
        'registrado_por',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_consumo' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Consumo $consumo): void {
            if (empty($consumo->cantidad)) {
                $consumo->cantidad = 1;
            }

            if (empty($consumo->estado)) {
                $consumo->estado = 'Confirmado';
            }

            if (empty($consumo->registrado_por) && Auth::check()) {
                $consumo->registrado_por = Auth::id();
            }

            $consumo->total = $consumo->calcularTotal();
        });

        static::saving(function (Consumo $consumo): void {
            if (empty($consumo->cantidad)) {
                $consumo->cantidad = 1;
            }

            $consumo->total = $consumo->calcularTotal();

            if (empty($consumo->registrado_por) && Auth::check()) {
                $consumo->registrado_por = Auth::id();
            }
        });
    }

    public function reservacion(): BelongsTo
    {
        return $this->belongsTo(Reservacion::class, 'reservacion_id');
    }

    public function huesped(): BelongsTo
    {
        return $this->belongsTo(Huesped::class, 'huesped_id');
    }

    public function habitacion(): BelongsTo
    {
        return $this->belongsTo(Habitacion::class, 'habitacion_id');
    }

    public function plato(): BelongsTo
    {
        return $this->belongsTo(Plato::class, 'plato_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function calcularTotal(): float
    {
        $cantidad = $this->cantidad ?? 1;
        $precio = $this->precio_unitario ?? 0;

        return (float) $cantidad * (float) $precio;
    }
}

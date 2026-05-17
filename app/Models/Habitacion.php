<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habitacion extends Model
{
    use SoftDeletes;

    protected $table = 'habitaciones';

    protected $fillable = [
        'numero',
        'tipo',
        'capacidad',
        'precio_noche',
        'estado',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'capacidad' => 'integer',
        'precio_noche' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function reservaciones(): HasMany
    {
        return $this->hasMany(Reservacion::class, 'habitacion_id');
    }

    public function getEstadoVisualAttribute(): string
    {
        if (in_array($this->estado, ['Mantenimiento', 'Inactiva'])) {
            return $this->estado;
        }

        $tieneReserva = $this->reservaciones()
            ->whereIn('estado_reservacion', ['Pendiente de pago', 'Confirmada'])
            ->where('fecha_entrada', '<=', now()->toDateString())
            ->where('fecha_salida', '>', now()->toDateString())
            ->exists();

        return $tieneReserva ? 'Reservada' : 'Disponible';
    }
}
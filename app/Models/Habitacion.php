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
        if ($this->estado === 'Mantenimiento') {
            return 'Mantenimiento';
        }

        if ($this->estado === 'Inactiva') {
            return 'Inactiva';
        }

        $tieneReservaActiva = $this->reservaciones()
            ->whereIn('estado_reservacion', ['Pendiente de pago', 'Confirmada', 'En estadía'])
            ->exists();

        return $tieneReservaActiva ? 'Reservada' : 'Disponible';
    }
}
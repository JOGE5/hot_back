<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Huesped extends Model
{
    use SoftDeletes;

    protected $table = 'huespedes';

    protected $fillable = [
        'user_id',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'tipo_documento',
        'numero_documento',
        'telefono',
        'correo_electronico',
        'nacionalidad',
        'fecha_nacimiento',
        'estado',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'estado' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reservaciones(): HasMany
    {
        return $this->hasMany(Reservacion::class, 'huesped_id');
    }

    public function getEstadoReservaVisualAttribute(): string
    {
        $tieneReserva = $this->reservaciones()
            ->whereIn('estado_reservacion', ['Pendiente de pago', 'Confirmada'])
            ->exists();

        return $tieneReserva ? 'En reservación' : 'Sin reserva activa';
    }
}
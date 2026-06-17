<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservacionAcompanante extends Model
{
    protected $table = 'reservacion_acompanantes';

    protected $fillable = [
        'reservacion_id',
        'nombre_completo',
        'tipo_documento',
        'numero_documento',
        'nacionalidad',
        'fecha_nacimiento',
        'nombre',
        'documento',
        'edad',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'edad' => 'integer',
    ];

    public function reservacion(): BelongsTo
    {
        return $this->belongsTo(Reservacion::class, 'reservacion_id');
    }

    public function getNombreCompletoAttribute($value): ?string
    {
        return $value ?: $this->nombre;
    }

    public function getNumeroDocumentoAttribute($value): ?string
    {
        return $value ?: $this->documento;
    }
}

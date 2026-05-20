<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paquete extends Model
{
    use SoftDeletes;

    protected $table = 'paquetes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen',
        'tipo_habitacion',
        'tour_incluido',
        'tour_id',
        'incluye_desayuno',
        'incluye_almuerzo',
        'incluye_cena',
        'duracion_dias',
        'precio_total',
        'estado',
        'observacion',
    ];

    protected $casts = [
        'incluye_desayuno' => 'boolean',
        'incluye_almuerzo' => 'boolean',
        'incluye_cena' => 'boolean',
        'duracion_dias' => 'integer',
        'precio_total' => 'decimal:2',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tour extends Model
{
    use SoftDeletes;

    protected $table = 'tours';

    protected $fillable = [
        'nombre',
        'descripcion',
        'ubicacion',
        'duracion',
        'precio',
        'cupos_disponibles',
        'imagen',
        'estado',
        'observacion',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'cupos_disponibles' => 'integer',
    ];

    public function paquetes(): HasMany
    {
        return $this->hasMany(Paquete::class, 'tour_id');
    }
}

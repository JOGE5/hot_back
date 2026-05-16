<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
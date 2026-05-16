<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
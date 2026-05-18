<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plato extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'platos';

    protected $fillable = [
        'chef_id',
        'nombre',
        'descripcion',
        'categoria',
        'precio',
        'imagen',
        'estado',
        'tiempo_preparacion',
        'observacion',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'tiempo_preparacion' => 'integer',
    ];

    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_id');
    }
}

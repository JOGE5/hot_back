<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingrediente extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'ingredientes';

    protected $fillable = [
        'nombre',
        'unidad_medida',
        'stock_actual',
        'stock_minimo',
        'costo_unitario',
        'fecha_vencimiento',
        'proveedor',
        'observacion',
    ];

    protected $casts = [
        'stock_actual' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'costo_unitario' => 'decimal:2',
        'fecha_vencimiento' => 'date',
    ];

    public function getEstadoVisualAttribute(): string
    {
        if ($this->fecha_vencimiento && $this->fecha_vencimiento->isPast()) {
            return 'Vencido';
        }

        if ($this->stock_actual == 0) {
            return 'Agotado';
        }

        if ($this->stock_actual <= $this->stock_minimo) {
            return 'Bajo stock';
        }

        return 'Disponible';
    }

    public function platos()
    {
        return $this->belongsToMany(Plato::class, 'ingrediente_plato')
            ->withPivot('cantidad_requerida')
            ->withTimestamps();
    }
}

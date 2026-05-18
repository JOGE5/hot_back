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

    public function ingredientes()
    {
        return $this->belongsToMany(Ingrediente::class, 'ingrediente_plato')
            ->withPivot('cantidad_requerida')
            ->withTimestamps();
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_plato')
            ->withPivot('orden')
            ->withTimestamps();
    }

    public function tieneStockSuficiente(): bool
    {
        if ($this->ingredientes->isEmpty()) {
            return false;
        }

        foreach ($this->ingredientes as $ingrediente) {
            $cantidadRequerida = $ingrediente->pivot->cantidad_requerida;

            if ($ingrediente->estado_visual === 'Vencido') {
                return false;
            }

            if ($ingrediente->stock_actual <= 0 || $ingrediente->stock_actual < $cantidadRequerida) {
                return false;
            }
        }

        return true;
    }

    public function getEstadoStockVisualAttribute(): string
    {
        if ($this->ingredientes->isEmpty()) {
            return 'Sin ingredientes';
        }

        foreach ($this->ingredientes as $ingrediente) {
            if ($ingrediente->estado_visual === 'Vencido') {
                return 'Ingredientes vencidos';
            }
            if ($ingrediente->stock_actual <= 0 || $ingrediente->stock_actual < $ingrediente->pivot->cantidad_requerida) {
                return 'Stock insuficiente';
            }
        }

        return 'Stock suficiente';
    }
}

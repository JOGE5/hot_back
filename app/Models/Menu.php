<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $table = 'menus';

    protected $fillable = [
        'chef_id',
        'fecha_menu',
        'tipo_menu',
        'estado',
        'observacion',
    ];

    protected $casts = [
        'fecha_menu' => 'date',
    ];

    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    public function platos()
    {
        return $this->belongsToMany(Plato::class, 'menu_plato')
            ->withPivot('orden')
            ->withTimestamps();
    }
}

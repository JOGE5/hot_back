<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Relations\HasOne;


class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
    'role_id',
    'nombres',
    'apellido_paterno',
    'apellido_materno',
    'name',
    'email',
    'password',
    'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'estado' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function huesped(): HasOne
    {
        return $this->hasOne(Huesped::class, 'user_id');
    }
}
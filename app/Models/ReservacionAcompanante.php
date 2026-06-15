<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservacionAcompanante extends Model
{
    protected $table = 'reservacion_acompanantes';

    protected $fillable = [
        'reservacion_id',
        'nombre',
        'documento',
        'edad',
    ];

    protected $casts = [
        'edad' => 'integer',
    ];

    public function reservacion(): BelongsTo
    {
        return $this->belongsTo(Reservacion::class, 'reservacion_id');
    }
}

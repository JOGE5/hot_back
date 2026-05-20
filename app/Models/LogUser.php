<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogUser extends Model
{
    protected $table = 'logs_user';

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'rol',
        'accion',
        'modulo',
        'descripcion',
        'ip',
        'user_agent',
        'created_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

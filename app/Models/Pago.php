<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'reservacion_id',
        'monto',
        'metodo_pago',
        'estado_pago',
        'fecha_pago',
        'comprobante',
        'observacion',
        'registrado_por',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'datetime',
    ];

    public function reservacion(): BelongsTo
    {
        return $this->belongsTo(Reservacion::class, 'reservacion_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    protected static function booted()
    {
        static::creating(function ($pago) {
            if (!$pago->registrado_por) {
                $pago->registrado_por = Auth::id();
            }
        });

        static::saved(function ($pago) {
            $reservacion = $pago->reservacion;
            
            if (!$reservacion) return;

            if ($pago->estado_pago === 'Confirmado') {
                if (empty($pago->fecha_pago)) {
                    $pago->fecha_pago = now();
                    $pago->saveQuietly();
                }

                $reservacion->estado_pago = 'Confirmado';
                $reservacion->metodo_pago = $pago->metodo_pago;
                
                if (empty($reservacion->codigo_checkin)) {
                    $reservacion->codigo_checkin = 'CHK-' . strtoupper(substr(uniqid(), -6));
                }
                
                // Solo si no está en estadía o finalizada, la marcamos Confirmada
                if (in_array($reservacion->estado_reservacion, ['Pendiente de pago', 'Cancelada'])) {
                    $reservacion->estado_reservacion = 'Confirmada';
                }
                
                $reservacion->save();
            } elseif ($pago->estado_pago === 'Pendiente') {
                $reservacion->estado_pago = 'Pendiente';
                if ($reservacion->estado_reservacion === 'Confirmada') {
                    $reservacion->estado_reservacion = 'Pendiente de pago';
                }
                $reservacion->save();
            } elseif ($pago->estado_pago === 'Rechazado') {
                $reservacion->estado_pago = 'Pendiente'; // O rechazado en la reserva, pero Pendiente permite reintentar
                $reservacion->estado_reservacion = 'Cancelada';
                $reservacion->save();
            }
        });
    }
}
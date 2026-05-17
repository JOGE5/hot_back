<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservacion;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;

class SyncPagosAntiguos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotel:sync-pagos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea registros en la tabla pagos para reservaciones confirmadas que no tienen pago.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando sincronización de pagos antiguos...');

        $reservaciones = Reservacion::where('estado_pago', 'Confirmado')
            ->where('total', '>', 0)
            ->whereNotNull('metodo_pago')
            ->where('metodo_pago', '!=', '')
            ->get();

        $count = 0;

        foreach ($reservaciones as $reservacion) {
            $exists = Pago::where('reservacion_id', $reservacion->id)
                ->where('estado_pago', 'Confirmado')
                ->exists();

            if (!$exists) {
                Pago::create([
                    'reservacion_id' => $reservacion->id,
                    'monto' => $reservacion->total,
                    'metodo_pago' => $reservacion->metodo_pago,
                    'estado_pago' => 'Confirmado',
                    'fecha_pago' => $reservacion->updated_at ?? now(),
                    'registrado_por' => null,
                    'observacion' => 'Pago sincronizado desde reservación antigua',
                ]);
                
                $count++;
            }
        }

        $this->info("Sincronización completada. Se crearon {$count} pagos.");
    }
}

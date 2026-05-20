<?php

namespace App\Filament\Resources\Pagos\Pages;

use App\Filament\Resources\Pagos\PagoResource;
use App\Support\LogSistema;
use Filament\Resources\Pages\CreateRecord;

class CreatePago extends CreateRecord
{
    protected static string $resource = PagoResource::class;

    protected function afterCreate(): void
    {
        if ($this->record->estado_pago !== 'Confirmado') {
            return;
        }

        LogSistema::registrar(
            'CONFIRMAR_PAGO',
            'Pagos',
            'Pago confirmado #' . $this->record->id . ' para reservación #' . $this->record->reservacion_id . ' por Bs. ' . $this->record->monto . '.'
        );
    }
}

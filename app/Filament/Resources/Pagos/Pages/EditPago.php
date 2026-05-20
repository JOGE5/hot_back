<?php

namespace App\Filament\Resources\Pagos\Pages;

use App\Filament\Resources\Pagos\PagoResource;
use App\Support\LogSistema;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPago extends EditRecord
{
    protected static string $resource = PagoResource::class;

    protected ?string $estadoPagoAnterior = null;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->estadoPagoAnterior = $this->record->estado_pago;
    }

    protected function afterSave(): void
    {
        if ($this->estadoPagoAnterior === 'Confirmado' || $this->record->estado_pago !== 'Confirmado') {
            return;
        }

        LogSistema::registrar(
            'CONFIRMAR_PAGO',
            'Pagos',
            'Pago confirmado #' . $this->record->id . ' para reservación #' . $this->record->reservacion_id . ' por Bs. ' . $this->record->monto . '.'
        );
    }
}

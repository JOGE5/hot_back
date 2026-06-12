<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConsumosExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(
        private readonly Collection $consumos,
        private readonly float $totalGeneralConfirmado,
    ) {
    }

    public function collection(): Collection
    {
        $consumos = clone $this->consumos;

        $consumos->push((object) [
            'is_total_row' => true,
            'total_general_confirmado' => $this->totalGeneralConfirmado,
        ]);

        return $consumos;
    }

    public function headings(): array
    {
        return [
            'Fecha de consumo',
            'Huésped',
            'Habitación',
            'Plato',
            'Cantidad',
            'Precio unitario',
            'Total',
            'Estado',
            'Registrado por',
            'Observación',
        ];
    }

    public function map($consumo): array
    {
        if (isset($consumo->is_total_row) && $consumo->is_total_row) {
            return [
                '',
                '',
                '',
                '',
                '',
                'TOTAL GENERAL CONFIRMADO',
                $consumo->total_general_confirmado,
                '',
                '',
                '',
            ];
        }

        return [
            $consumo->fecha_consumo
                ? date('d/m/Y H:i', strtotime($consumo->fecha_consumo))
                : 'Sin fecha',
            $consumo->huesped_nombre,
            $consumo->habitacion_texto,
            $consumo->plato_nombre,
            (int) $consumo->cantidad,
            (float) $consumo->precio_unitario,
            (float) $consumo->total,
            $consumo->estado,
            $consumo->registrado_por_nombre,
            $consumo->observacion ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

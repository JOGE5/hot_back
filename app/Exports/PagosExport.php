<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PagosExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected Collection $pagos;

    public function __construct(Collection $pagos)
    {
        $this->pagos = $pagos;
    }

    public function collection()
    {
        $pagos = clone $this->pagos;
        
        $total = $pagos->sum('monto');
        
        $pagos->push((object)[
            'is_total_row' => true,
            'total_general' => $total,
        ]);

        return $pagos;
    }

    public function headings(): array
    {
        return [
            'Código Check-in',
            'Huésped',
            'Documento',
            'Habitación',
            'Método de Pago',
            'Monto (Bs.)',
            'Estado',
            'Fecha de Pago',
            'Registrado Por',
        ];
    }

    public function map($pago): array
    {
        if (isset($pago->is_total_row) && $pago->is_total_row) {
            return [
                '',
                '',
                '',
                '',
                'TOTAL GENERAL',
                $pago->total_general,
                '',
                '',
                '',
            ];
        }

        $huesped = $pago->reservacion?->huesped;
        $nombreHuesped = $huesped ? "{$huesped->nombres} {$huesped->apellido_paterno} {$huesped->apellido_materno}" : 'N/A';
        $documento = $huesped ? $huesped->numero_documento : 'N/A';
        
        $habitacion = $pago->reservacion?->habitacion;
        $numHabitacion = $habitacion ? $habitacion->numero : 'N/A';
        
        $registradoPor = $pago->registradoPor ? "{$pago->registradoPor->nombres} {$pago->registradoPor->apellido_paterno}" : 'N/A';

        return [
            $pago->reservacion?->codigo_checkin ?? 'N/A',
            $nombreHuesped,
            $documento,
            $numHabitacion,
            $pago->metodo_pago,
            $pago->monto,
            $pago->estado_pago,
            $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y H:i') : 'N/A',
            $registradoPor,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

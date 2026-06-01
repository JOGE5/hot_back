<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Database\Eloquent\Builder;

class HistorialCheckInExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Código Check-in',
            'Fecha/Hora Check-in',
            'Registrado check-in por',
            'Código Check-out',
            'Fecha/Hora Check-out',
            'Registrado check-out por',
            'Huésped',
            'Documento',
            'Habitación',
            'Tipo',
            'Fecha Entrada',
            'Fecha Salida',
            'Método Pago',
            'Total (Bs.)',
            'Estado',
        ];
    }

    public function map($reservacion): array
    {
        $huesped = $reservacion->huesped;
        $habitacion = $reservacion->habitacion;

        return [
            $reservacion->codigo_checkin,
            \Carbon\Carbon::parse($reservacion->checkin_at)->format('d/m/Y H:i'),
            $reservacion->checkinUser ? explode(' ', $reservacion->checkinUser->name)[0] : 'N/A',
            $reservacion->codigo_checkout ?? 'N/A',
            $reservacion->checkout_at ? \Carbon\Carbon::parse($reservacion->checkout_at)->format('d/m/Y H:i') : 'N/A',
            $reservacion->checkoutUser ? explode(' ', $reservacion->checkoutUser->name)[0] : 'N/A',
            $huesped ? trim($huesped->nombres . ' ' . $huesped->apellido_paterno . ' ' . ($huesped->apellido_materno ?? '')) : 'HuÃ©sped no disponible',
            $huesped?->numero_documento ?? 'N/A',
            $habitacion ? 'Hab. ' . $habitacion->numero : 'HabitaciÃ³n no disponible',
            $habitacion?->tipo ?? 'N/A',
            \Carbon\Carbon::parse($reservacion->fecha_entrada)->format('d/m/Y'),
            \Carbon\Carbon::parse($reservacion->fecha_salida)->format('d/m/Y'),
            $reservacion->metodo_pago,
            number_format($reservacion->total, 2),
            $reservacion->estado_reservacion,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

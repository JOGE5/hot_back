<?php

namespace App\Exports;

use App\Models\Huesped;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HuespedesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Huesped::where('estado', true)->get();
    }

    public function headings(): array
    {
        return [
            'Nombres',
            'Apellido paterno',
            'Apellido materno',
            'Tipo de documento',
            'Número de documento',
            'Teléfono',
            'Correo electrónico',
            'Nacionalidad',
            'Fecha de nacimiento',
            'Estado',
        ];
    }

    public function map($huesped): array
    {
        return [
            $huesped->nombres,
            $huesped->apellido_paterno,
            $huesped->apellido_materno,
            $huesped->tipo_documento,
            $huesped->numero_documento,
            $huesped->telefono,
            $huesped->correo_electronico,
            $huesped->nacionalidad,
            $huesped->fecha_nacimiento ? $huesped->fecha_nacimiento->format('d/m/Y') : '',
            $huesped->estado ? 'Activo' : 'Inactivo',
        ];
    }
}

<?php

namespace App\Exports;

use App\Support\Admin\ReporteDinamicoService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteDinamicoExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(
        private readonly string $modulo,
        private readonly Collection $registros,
        private readonly array $columnas,
    ) {
    }

    public function collection(): Collection
    {
        return $this->registros;
    }

    public function headings(): array
    {
        return array_values($this->columnas);
    }

    public function map($registro): array
    {
        $service = app(ReporteDinamicoService::class);

        return collect(array_keys($this->columnas))
            ->map(fn (string $columna): string => $service->valor($this->modulo, $registro, $columna))
            ->all();
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

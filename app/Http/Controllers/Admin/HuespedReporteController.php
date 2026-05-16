<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Huesped;
use App\Exports\HuespedesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class HuespedReporteController extends Controller
{
    public function exportarExcel()
    {
        return Excel::download(new HuespedesExport, 'reporte_huespedes.xlsx');
    }

    public function exportarPdf()
    {
        $huespedes = Huesped::where('estado', true)->get();
        $pdf = Pdf::loadView('reportes.huespedes_pdf', compact('huespedes'))
                  ->setPaper('a4', 'landscape');
        
        return $pdf->download('reporte_huespedes.pdf');
    }
}

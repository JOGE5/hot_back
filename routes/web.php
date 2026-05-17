<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\Admin\HuespedReporteController;
use App\Http\Controllers\Admin\CheckInReporteController;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/reportes/huespedes/excel', [HuespedReporteController::class, 'exportarExcel'])->name('reportes.huespedes.excel');
    Route::get('/reportes/huespedes/pdf', [HuespedReporteController::class, 'exportarPdf'])->name('reportes.huespedes.pdf');
    
    Route::get('/reportes/check-in/excel', [CheckInReporteController::class, 'excel'])->name('reportes.check-in.excel');
    Route::get('/reportes/check-in/pdf', [CheckInReporteController::class, 'pdf'])->name('reportes.check-in.pdf');
});

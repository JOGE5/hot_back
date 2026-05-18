<?php

use App\Http\Controllers\MenuPublicadoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/menu-del-dia', MenuPublicadoController::class)->name('menu-publicado');

use App\Http\Controllers\Admin\HuespedReporteController;
use App\Http\Controllers\Admin\CheckInReporteController;
use App\Http\Controllers\Admin\ReciboReservacionController;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/reportes/huespedes/excel', [HuespedReporteController::class, 'exportarExcel'])->name('reportes.huespedes.excel');
    Route::get('/reportes/huespedes/pdf', [HuespedReporteController::class, 'exportarPdf'])->name('reportes.huespedes.pdf');
    
    Route::get('/reportes/check-in/excel', [CheckInReporteController::class, 'excel'])->name('reportes.check-in.excel');
    Route::get('/reportes/check-in/pdf', [CheckInReporteController::class, 'pdf'])->name('reportes.check-in.pdf');

    Route::get('/reservaciones/{reservacion}/recibo', [ReciboReservacionController::class, 'generarPdf'])->name('reservaciones.recibo');
    Route::post('/reservaciones/{reservacion}/enviar-recibo', [ReciboReservacionController::class, 'enviarCorreo'])->name('reservaciones.enviar-recibo');
});

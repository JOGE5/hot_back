<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HuespedController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    Route::prefix('huesped')->name('api.huesped.')->group(function () {
        Route::get('/dashboard', [HuespedController::class, 'dashboard'])->name('dashboard');
        Route::get('/habitaciones-disponibles', [HuespedController::class, 'habitacionesDisponibles']);
        Route::get('/mis-reservaciones', [HuespedController::class, 'misReservaciones']);
        Route::post('/reservaciones', [HuespedController::class, 'crearReservacion']);
        Route::get('/menu-del-dia', [HuespedController::class, 'menuDelDia']);
        Route::get('/paquetes', [HuespedController::class, 'paquetes']);
    });
});

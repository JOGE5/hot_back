<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\HuespedController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/verify-login-code', [AuthController::class, 'verifyLoginCode'])->name('api.verify-login-code');
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('api.auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('api.auth.google.callback');
Route::post('/auth/google/complete-register', [GoogleAuthController::class, 'completeRegister'])->name('api.auth.google.complete-register');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    Route::prefix('huesped')->name('api.huesped.')->group(function () {
        Route::post('/cambiar-password', [HuespedController::class, 'cambiarPassword'])->name('cambiar-password');
        Route::get('/dashboard', [HuespedController::class, 'dashboard'])->name('dashboard');
        Route::get('/habitaciones-disponibles', [HuespedController::class, 'habitacionesDisponibles']);
        Route::get('/mis-reservaciones', [HuespedController::class, 'misReservaciones']);
        Route::post('/reservaciones', [HuespedController::class, 'crearReservacion']);
        Route::patch('/reservaciones/{reservacion}/cancelar', [HuespedController::class, 'cancelarReservacion']);
        Route::patch('/reservaciones/{reservacion}/posponer', [HuespedController::class, 'posponerReservacion']);
        Route::get('/menu-del-dia', [HuespedController::class, 'menuDelDia']);
        Route::get('/paquetes', [HuespedController::class, 'paquetes']);
    });
});

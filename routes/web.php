<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OfertaController;
use App\Http\Controllers\DashboardController;

// Ruta para la página principal
Route::get('/', function () {
    return view('welcome'); // Cambia 'welcome' por la vista principal que desees
})->name('home');

// Rutas para autenticación
Route::get('login', [AuthController::class, 'loginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('register', [AuthController::class, 'registerForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);

// Rutas para el Dashboard
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Rutas para las ofertas de reciclaje
Route::middleware('auth')->group(function () {
    // Vendedor puede crear una oferta
    Route::get('ofertas', [OfertaController::class, 'index'])->name('oferta.index');
    Route::get('ofertas/create', [OfertaController::class, 'create'])->name('oferta.create'); // Cambiado a 'ofertas/create'
    Route::post('ofertas', [OfertaController::class, 'store'])->name('oferta.store'); // Cambiado a 'ofertas'
    
    // Recolector puede ver las ofertas, aceptar o rechazar
    Route::get('oferta/{id}', [OfertaController::class, 'show'])->name('oferta.show');
    Route::post('oferta/{id}/aceptar', [OfertaController::class, 'aceptarOferta'])->name('oferta.aceptar');
    Route::post('oferta/{id}/rechazar', [OfertaController::class, 'rechazarOferta'])->name('oferta.rechazar');
});

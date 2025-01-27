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
    Route::get('ofertas/{id}/edit', [OfertaController::class, 'edit'])->name('oferta.edit');
Route::put('ofertas/{id}', [OfertaController::class, 'update'])->name('oferta.update');
Route::delete('ofertas/{id}', [OfertaController::class, 'destroy'])->name('oferta.destroy');

    // Recolector puede ver las ofertas, aceptar o rechazar
    Route::get('oferta/{id}', [OfertaController::class, 'show'])->name('oferta.show');
    Route::post('oferta/{id}/aceptar', [OfertaController::class, 'aceptarOferta'])->name('oferta.aceptar');
    Route::post('oferta/{id}/rechazar', [OfertaController::class, 'rechazarOferta'])->name('oferta.rechazar');
    Route::post('oferta/{id}/completar', [OfertaController::class, 'completarOferta'])->name('oferta.completar');


});


// Rutas para la edición de perfil
Route::middleware('auth')->group(function () {
    // Ruta para la edición del perfil
    Route::get('/profile/edit', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
});

Route::get('/indicaciones/{id}', [OfertaController::class, 'indicaciones'])->name('oferta.indicaciones');








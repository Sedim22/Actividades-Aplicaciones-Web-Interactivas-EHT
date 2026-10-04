<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecetaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return to_route($request->user() ? 'recetas.index' : 'login');
})->name('inicio');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.store');
    Route::get('/registro', [AuthController::class, 'register'])->name('register');
    Route::post('/registro', [AuthController::class, 'store'])->name('register.store');
});

// Todas las rutas del recetario requieren una sesión iniciada.
Route::middleware('auth')->group(function () {
    Route::resource('recetas', RecetaController::class)
        ->parameters(['recetas' => 'receta']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

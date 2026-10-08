<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InscripcionesAdminController;
use App\Http\Controllers\InscripcionesController;
use App\Http\Controllers\TorneosController;
use App\Http\Controllers\TorneosPublicosController;
use Illuminate\Support\Facades\Route;

// Todas estas rutas reciben también el grupo web (sesión y protección CSRF).
Route::view('/', 'welcome')
    ->middleware('rol:guest,jugador,admin')
    ->name('inicio');

Route::middleware('rol:guest,jugador,admin')->group(function () {
    Route::get('/torneos', [TorneosPublicosController::class, 'index'])->name('torneos.index');
    Route::get('/torneos/{torneo}', [TorneosPublicosController::class, 'show'])->name('torneos.show');
});

Route::middleware('guest')->group(function () {
    Route::view('/registro', 'auth.registro')->name('registro');
    Route::post('/registro', [AuthController::class, 'registrar'])->name('registro.store');
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/panel', [AuthController::class, 'panel'])
        ->middleware('rol:jugador,admin')
        ->name('panel');

    Route::view('/admin', 'admin.inicio')
        ->middleware('rol:admin')
        ->name('admin.inicio');

    Route::prefix('admin/torneos')->name('admin.torneos.')->middleware('rol:admin')->group(function () {
        Route::get('/', [TorneosController::class, 'index'])->name('index');
        Route::get('/crear', [TorneosController::class, 'create'])->name('create');
        Route::post('/', [TorneosController::class, 'store'])->name('store');
        Route::get('/{torneo}/editar', [TorneosController::class, 'edit'])->name('edit');
        Route::put('/{torneo}', [TorneosController::class, 'update'])->name('update');
        Route::delete('/{torneo}', [TorneosController::class, 'destroy'])->name('destroy');
        Route::get('/{torneo}/inscripciones', [InscripcionesAdminController::class, 'index'])->name('inscripciones.index');
        Route::delete('/{torneo}/inscripciones/{inscripcion}', [InscripcionesAdminController::class, 'destroy'])->name('inscripciones.destroy');
    });

    Route::middleware('rol:jugador')->group(function () {
        Route::get('/mis-torneos', [InscripcionesController::class, 'index'])->name('inscripciones.index');
        Route::post('/torneos/{torneo}/inscripciones', [InscripcionesController::class, 'store'])->name('inscripciones.store');
        Route::delete('/torneos/{torneo}/inscripciones', [InscripcionesController::class, 'destroy'])->name('inscripciones.destroy');
    });

    Route::view('/jugador', 'jugador.inicio')
        ->middleware('rol:jugador')
        ->name('jugador.inicio');
});

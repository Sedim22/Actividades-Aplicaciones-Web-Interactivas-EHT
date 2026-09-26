<?php

use App\Http\Controllers\TasksController;
use Illuminate\Support\Facades\Route;

//Tablero con las tareas agrupadas por estados filtro y buscador
Route::get('tasks',[TasksController::class,'index'])->name('tasks.index');
//Formulario para crear una nueva tarea
Route::get('tasks/crear',[TasksController::class,'create'])->name('tasks.create');
//Guardar la tarea nueva
Route::post('tasks',[TasksController::class,'store'])->name('tasks.store');
//Cambio rapido de estado desde los botones de cada tarjeta
Route::patch('tasks/{task}/estado',[TasksController::class,'changeStatus'])->name('tasks.change-status');
//Detalle de una tarea
Route::get('tasks/{task}',[TasksController::class,'show'])->name('tasks.show');
//Formulario para editar una tarea
Route::get('tasks/{task}/editar',[TasksController::class,'edit'])->name('tasks.edit');
//Guarda los cambios de la tarea.
Route::match(['put','patch'],'tasks/{task}',[TasksController::class,'update'])->name('tasks.update');
//Eliminar tarea de forma definitiva
Route::delete('tasks/{task}',[TasksController::class,'destroy'])->name('tasks.destroy');
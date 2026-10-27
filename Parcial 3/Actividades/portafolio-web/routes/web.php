<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/experiencia', function () {
    return view('experience');
})->name('experience');

Route::get('/proyectos', function () {
    return view('projects');
})->name('projects');

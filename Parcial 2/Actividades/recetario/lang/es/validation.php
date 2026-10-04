<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser texto.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'in' => 'Selecciona una opción válida para :attribute.',
    'alpha_dash' => 'El nombre de usuario solo puede contener letras sin acentos, números, guiones y guiones bajos.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'unique' => 'Este :attribute ya está registrado.',
    'min' => [
        'numeric' => 'El campo :attribute debe ser como mínimo :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'max' => [
        'numeric' => 'El campo :attribute no debe superar :max.',
        'string' => 'El campo :attribute no debe superar :max caracteres.',
    ],
    'attributes' => [
        'name' => 'nombre de usuario',
        'password' => 'contraseña',
        'titulo' => 'título',
        'categoria' => 'categoría',
        'tiempo' => 'tiempo en minutos',
        'dificultad' => 'dificultad',
        'ingredientes' => 'ingredientes',
        'pasos' => 'pasos de preparación',
        'nota' => 'nota personal',
        'buscar' => 'búsqueda',
    ],
];

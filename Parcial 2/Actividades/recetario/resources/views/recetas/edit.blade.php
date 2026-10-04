@extends('layouts.app')
@section('title', 'Editar receta')
@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('recetas.show', $receta) }}" class="text-link text-sm">← Volver a la receta</a>
        <p class="eyebrow mb-3 mt-7">Un toque personal</p>
        <h1 class="page-title">Editar receta</h1>
        <p class="mb-7 mt-3 text-stone-600">Ajusta lo que necesites y guarda tu nueva versión.</p>
        <form action="{{ route('recetas.update', $receta) }}" method="POST" class="card p-6 sm:p-8">
            @csrf
            @method('PUT')
            @include('recetas.partials.form', ['boton' => 'Guardar cambios'])
        </form>
    </div>
@endsection

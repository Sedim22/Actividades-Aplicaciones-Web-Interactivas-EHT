@extends('layouts.app')
@section('title', 'Nueva receta')
@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('recetas.index') }}" class="text-link text-sm">← Volver a mis recetas</a>
        <p class="eyebrow mb-3 mt-7">Algo rico empieza aquí</p>
        <h1 class="page-title">Nueva receta</h1>
        <p class="mb-7 mt-3 text-stone-600">Anota los ingredientes y cada paso para repetirla cuando quieras.</p>
        <form action="{{ route('recetas.store') }}" method="POST" class="card p-6 sm:p-8">
            @csrf
            @include('recetas.partials.form', ['boton' => 'Guardar receta'])
        </form>
    </div>
@endsection

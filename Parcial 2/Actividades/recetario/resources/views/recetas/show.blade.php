@extends('layouts.app')
@section('title', e($receta->titulo))
@section('content')
    <a href="{{ route('recetas.index') }}" class="text-link text-sm">← Volver a mis recetas</a>
    <div class="mb-9 mt-7 flex flex-wrap items-start justify-between gap-6">
        <div class="min-w-0 flex-1">
            <p class="eyebrow mb-3">{{ \App\Models\Receta::CATEGORIAS[$receta->categoria] }}</p>
            <h1 class="page-title break-words">{{ $receta->titulo }}</h1>
            <div class="mt-5 flex flex-wrap gap-3 text-sm">
                <span class="rounded-full border border-stone-200 bg-white px-4 py-2">{{ $receta->tiempo }} minutos</span>
                <span class="rounded-full border border-stone-200 bg-white px-4 py-2">Dificultad: {{ \App\Models\Receta::DIFICULTADES[$receta->dificultad] }}</span>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('recetas.edit', $receta) }}" class="btn btn-primary">Editar receta</a>
            <form action="{{ route('recetas.destroy', $receta) }}" method="POST" data-confirm="¿Quieres eliminar esta receta? Esta acción no se puede deshacer.">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </form>
        </div>
    </div>

    <div class="grid items-start gap-6 md:grid-cols-[1fr_1.6fr]">
        <section class="card p-6 sm:p-8" aria-labelledby="ingredientes-title">
            <h2 id="ingredientes-title" class="mb-5 font-serif text-2xl">Ingredientes</h2>
            <ul class="list-disc space-y-3 pl-5 leading-relaxed marker:text-emerald-700">
                @foreach ($receta->ingredientesComoLista() as $ingrediente)
                    <li class="break-words pl-1">{{ $ingrediente }}</li>
                @endforeach
            </ul>
        </section>
        <section class="card p-6 sm:p-8" aria-labelledby="pasos-title">
            <h2 id="pasos-title" class="mb-5 font-serif text-2xl">Preparación</h2>
            <ol class="list-decimal space-y-5 pl-6 leading-relaxed marker:font-bold marker:text-emerald-800">
                @foreach ($receta->pasosComoLista() as $paso)
                    <li class="break-words pl-2">{{ $paso }}</li>
                @endforeach
            </ol>
        </section>
    </div>
    @if ($receta->nota)
        <section class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-6 sm:p-8" aria-labelledby="nota-title">
            <h2 id="nota-title" class="font-serif text-2xl text-amber-950">Mi nota personal</h2>
            <p class="mt-3 whitespace-pre-line break-words leading-relaxed text-amber-950">{{ $receta->nota }}</p>
        </section>
    @endif
@endsection

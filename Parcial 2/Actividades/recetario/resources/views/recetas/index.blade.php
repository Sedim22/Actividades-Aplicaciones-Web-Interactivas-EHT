@extends('layouts.app')
@section('title', 'Mis recetas')
@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-5">
        <div>
            <p class="eyebrow mb-3">Mi colección</p>
            <h1 class="page-title">Mis recetas</h1>
            <p class="mt-3 text-stone-600">Un poco de inspiración para tu próxima comida.</p>
        </div>
        <a href="{{ route('recetas.create') }}" class="btn btn-primary"><span aria-hidden="true">+</span> Nueva receta</a>
    </div>

    <form action="{{ route('recetas.index') }}" method="GET" class="card mb-7 grid items-start gap-4 p-5 sm:grid-cols-[1fr_1fr_auto]">
        <div>
            <label for="buscar" class="field-label">Buscar por título</label>
            <input id="buscar" name="buscar" value="{{ old('buscar', $buscar) }}" placeholder="¿Qué quieres cocinar?" maxlength="255" class="field" aria-invalid="{{ $errors->has('buscar') ? 'true' : 'false' }}" @error('buscar') aria-describedby="buscar-error" @enderror>
            <x-field-error name="buscar" />
        </div>
        <div>
            <label for="categoria" class="field-label">Categoría</label>
            <select id="categoria" name="categoria" class="field" aria-invalid="{{ $errors->has('categoria') ? 'true' : 'false' }}" @error('categoria') aria-describedby="categoria-error" @enderror>
                <option value="">Todas las categorías</option>
                @foreach (\App\Models\Receta::CATEGORIAS as $valor => $etiqueta)
                    <option value="{{ $valor }}" @selected(old('categoria', $categoria) === $valor)>{{ $etiqueta }}</option>
                @endforeach
            </select>
            <x-field-error name="categoria" />
        </div>
        <button type="submit" class="btn btn-primary sm:mt-7">Buscar</button>
    </form>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3 text-sm text-stone-500">
        <p>{{ $recetas->total() }} {{ $recetas->total() === 1 ? 'receta encontrada' : 'recetas encontradas' }}</p>
        @if ($buscar !== '' || $categoria !== '')
            <a href="{{ route('recetas.index') }}" class="text-link">Limpiar filtros</a>
        @endif
    </div>

    @if ($recetas->isNotEmpty())
        <div class="card overflow-x-auto">
            <table class="w-full text-left text-sm">
                <caption class="sr-only">Mis recetas: título, categoría, tiempo y dificultad</caption>
                <thead class="border-b border-stone-200 bg-stone-100/70 text-xs uppercase tracking-wide text-stone-600">
                    <tr>
                        <th scope="col" class="px-6 py-4">Título</th>
                        <th scope="col" class="px-5 py-4">Categoría</th>
                        <th scope="col" class="px-5 py-4">Tiempo</th>
                        <th scope="col" class="px-5 py-4">Dificultad</th>
                        <th scope="col" class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($recetas as $receta)
                        <tr class="hover:bg-stone-50">
                            <th scope="row" class="min-w-48 max-w-xs break-words px-6 py-5 font-semibold"><a href="{{ route('recetas.show', $receta) }}" class="text-link">{{ $receta->titulo }}</a></th>
                            <td class="px-5 py-5"><span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-900">{{ \App\Models\Receta::CATEGORIAS[$receta->categoria] }}</span></td>
                            <td class="whitespace-nowrap px-5 py-5">{{ $receta->tiempo }} min</td>
                            <td class="px-5 py-5">{{ \App\Models\Receta::DIFICULTADES[$receta->dificultad] }}</td>
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-end gap-4">
                                    <a href="{{ route('recetas.edit', $receta) }}" class="text-link" aria-label="Editar {{ $receta->titulo }}">Editar</a>
                                    <form action="{{ route('recetas.destroy', $receta) }}" method="POST" data-confirm="¿Quieres eliminar esta receta? Esta acción no se puede deshacer.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="py-2 font-semibold text-red-700 hover:underline" aria-label="Eliminar {{ $receta->titulo }}">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($recetas->hasPages())
            <nav aria-label="Paginación de recetas" class="mt-6 flex flex-wrap items-center justify-between gap-4 text-sm">
                @if ($recetas->onFirstPage())
                    <span class="btn btn-secondary opacity-50" aria-disabled="true">Anterior</span>
                @else
                    <a href="{{ $recetas->previousPageUrl() }}" class="btn btn-secondary" rel="prev">Anterior</a>
                @endif
                <span>Página {{ $recetas->currentPage() }} de {{ $recetas->lastPage() }}</span>
                @if ($recetas->hasMorePages())
                    <a href="{{ $recetas->nextPageUrl() }}" class="btn btn-secondary" rel="next">Siguiente</a>
                @else
                    <span class="btn btn-secondary opacity-50" aria-disabled="true">Siguiente</span>
                @endif
            </nav>
        @endif
    @else
        <section class="card px-6 py-16 text-center">
            <span aria-hidden="true" class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-2xl text-emerald-800">{{ $hayRecetas ? '⌕' : '+' }}</span>
            <h2 class="font-serif text-2xl">{{ $hayRecetas ? 'No encontramos recetas' : 'Tu recetario está por comenzar' }}</h2>
            <p class="mx-auto mt-3 max-w-md leading-relaxed text-stone-500">{{ $hayRecetas ? 'No hay resultados que coincidan con tu búsqueda y categoría. Prueba con otros filtros.' : 'Aún no tienes recetas. Guarda la primera y empieza a reunir los sabores de tu cocina.' }}</p>
            <a href="{{ $hayRecetas ? route('recetas.index') : route('recetas.create') }}" class="btn btn-primary mt-6">{{ $hayRecetas ? 'Ver todas mis recetas' : 'Crear mi primera receta' }}</a>
        </section>
    @endif
@endsection

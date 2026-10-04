@extends('layouts.app')
@section('title')@yield('error-title')@endsection
@section('content')
    <section class="card mx-auto my-8 max-w-xl px-6 py-12 text-center sm:px-10">
        <p class="mb-5 font-serif text-6xl text-emerald-800">@yield('code')</p>
        <h1 class="page-title">@yield('error-title')</h1>
        <p class="mt-5 leading-relaxed text-stone-600">@yield('description')</p>
        <a href="{{ auth()->check() ? route('recetas.index') : route('login') }}" class="btn btn-primary mt-8">{{ auth()->check() ? 'Volver a mis recetas' : 'Iniciar sesión' }}</a>
    </section>
@endsection

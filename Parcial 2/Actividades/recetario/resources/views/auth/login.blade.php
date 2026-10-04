@extends('layouts.app')
@section('title', 'Iniciar sesión')
@section('content')
    <div class="grid items-center gap-10 py-4 md:grid-cols-2 md:gap-16 md:py-12">
        <div>
            <p class="eyebrow mb-5">Tu cocina, a tu manera</p>
            <h1 class="font-serif text-4xl leading-tight text-stone-900 sm:text-5xl">Un lugar para tus<br><span class="text-emerald-800">recetas favoritas.</span></h1>
            <p class="mt-6 max-w-md text-lg leading-relaxed text-stone-600">Guarda ese desayuno de siempre, un postre especial o la receta que acabas de descubrir. Tu recetario te espera.</p>
            <div class="mt-8 flex flex-wrap gap-2 text-sm text-emerald-900">
                <span class="rounded-full bg-emerald-100 px-4 py-2">Guarda</span>
                <span class="rounded-full bg-emerald-100 px-4 py-2">Organiza</span>
                <span class="rounded-full bg-emerald-100 px-4 py-2">Vuelve a cocinar</span>
            </div>
        </div>
        <section aria-labelledby="login-title" class="card p-6 sm:p-9">
            <h2 id="login-title" class="font-serif text-3xl text-stone-900">Bienvenido a tu cocina</h2>
            <p class="mt-2 text-sm text-stone-500">Inicia sesión con tu nombre de usuario y contraseña.</p>
            <form action="{{ route('login.store') }}" method="POST" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="name" class="field-label">Nombre de usuario</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" autocomplete="username" autofocus class="field" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" @error('name') aria-describedby="name-error" @enderror>
                    <x-field-error name="name" />
                </div>
                <div>
                    <label for="password" class="field-label">Contraseña</label>
                    <input id="password" name="password" type="password" required maxlength="72" autocomplete="current-password" class="field" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" @error('password') aria-describedby="password-error" @enderror>
                    <x-field-error name="password" />
                </div>
                <button type="submit" class="btn btn-primary w-full">Entrar a mi recetario <span aria-hidden="true">→</span></button>
            </form>
            <p class="mt-6 text-center text-sm text-stone-600">¿Es tu primera visita? <a href="{{ route('register') }}" class="text-link">Crea tu cuenta</a></p>
        </section>
    </div>
@endsection

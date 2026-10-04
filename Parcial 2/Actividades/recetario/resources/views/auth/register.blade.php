@extends('layouts.app')
@section('title', 'Crear cuenta')
@section('content')
    <section class="card mx-auto max-w-lg p-6 sm:p-9">
        <p class="eyebrow mb-3">Empieza tu colección</p>
        <h1 class="page-title">Crea tu cuenta</h1>
        <p class="mt-3 text-stone-600">Un recetario solo tuyo, listo para tu primera receta.</p>
        <form action="{{ route('register.store') }}" method="POST" class="mt-7 space-y-5">
            @csrf
            <div>
                <label for="name" class="field-label">Nombre de usuario</label>
                <input id="name" name="name" value="{{ old('name') }}" required minlength="3" maxlength="50" autocomplete="username" autofocus class="field" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" aria-describedby="name-help{{ $errors->has('name') ? ' name-error' : '' }}">
                <p id="name-help" class="mt-2 text-xs leading-relaxed text-stone-500">De 3 a 50 caracteres. Usa letras sin acentos, números, guiones o guiones bajos.</p>
                <x-field-error name="name" />
            </div>
            <div>
                <label for="password" class="field-label">Contraseña</label>
                <input id="password" name="password" type="password" required minlength="8" maxlength="72" autocomplete="new-password" class="field" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" aria-describedby="password-help{{ $errors->has('password') ? ' password-error' : '' }}">
                <p id="password-help" class="mt-2 text-xs text-stone-500">Usa al menos 8 caracteres.</p>
                <x-field-error name="password" />
            </div>
            <div>
                <label for="password_confirmation" class="field-label">Confirmar contraseña</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" maxlength="72" autocomplete="new-password" class="field">
            </div>
            <button class="btn btn-primary w-full" type="submit">Crear mi cuenta</button>
        </form>
        <p class="mt-6 text-center text-sm text-stone-600">¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-link">Inicia sesión</a></p>
    </section>
@endsection

@props(['name', 'label', 'type' => 'text', 'value' => null, 'options' => [], 'help' => null, 'required' => false])

@php
    // No se vuelven a mostrar contraseñas ni entradas de tipo arreglo.
    $valor = $type === 'password' ? '' : old($name, $value);
    $valor = is_scalar($valor) ? (string) $valor : '';
    $invalido = $errors->has($name);
    $valido = $errors->any() && session()->hasOldInput($name) && ! $invalido
        && $type !== 'password' && $valor !== '';
    $descripcion = trim(($help ? $name.'-ayuda ' : '').($invalido ? $name.'-error' : ($valido ? $name.'-correcto' : '')));
    $control = $attributes->class([
        $type === 'select' ? 'form-select' : 'form-control',
        'is-invalid' => $invalido,
        'is-valid' => $valido,
    ])->merge(['id' => $name, 'name' => $name, 'aria-invalid' => $invalido ? 'true' : 'false', 'aria-describedby' => $descripcion ?: null]);
@endphp

<label for="{{ $name }}" class="form-label">{{ $label }}</label>
@if ($type === 'select')
    <select {{ $control }} @required($required)>
        @foreach ($options as $clave => $etiqueta)
            <option value="{{ $clave }}" @selected($valor === (string) $clave)>{{ $etiqueta }}</option>
        @endforeach
    </select>
@elseif ($type === 'textarea')
    <textarea {{ $control }} @required($required)>{{ $valor }}</textarea>
@else
    <input type="{{ $type }}" {{ $control }} value="{{ $valor }}" @required($required)>
@endif
@if ($help)
    <div id="{{ $name }}-ayuda" class="form-text">{{ $help }}</div>
@endif
@if ($invalido)
    <div id="{{ $name }}-error" class="invalid-feedback" role="alert">{{ $errors->first($name) }}</div>
@elseif ($valido)
    <div id="{{ $name }}-correcto" class="valid-feedback">El formato de este campo es válido.</div>
@endif

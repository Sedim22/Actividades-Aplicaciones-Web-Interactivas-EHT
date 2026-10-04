<p class="mb-6 text-sm text-stone-500">Todos los campos son obligatorios, excepto la nota personal.</p>
<div class="space-y-6">
    <div>
        <label for="titulo" class="field-label">Título</label>
        <input id="titulo" name="titulo" value="{{ old('titulo', $receta->titulo) }}" required maxlength="255" placeholder="Por ejemplo: enchiladas verdes" class="field" aria-invalid="{{ $errors->has('titulo') ? 'true' : 'false' }}" @error('titulo') aria-describedby="titulo-error" @enderror>
        <x-field-error name="titulo" />
    </div>
    <div class="grid gap-5 sm:grid-cols-3">
        <div>
            <label for="categoria" class="field-label">Categoría</label>
            <select id="categoria" name="categoria" required class="field" aria-invalid="{{ $errors->has('categoria') ? 'true' : 'false' }}" @error('categoria') aria-describedby="categoria-error" @enderror>
                <option value="">Selecciona</option>
                @foreach (\App\Models\Receta::CATEGORIAS as $valor => $etiqueta)
                    <option value="{{ $valor }}" @selected(old('categoria', $receta->categoria) === $valor)>{{ $etiqueta }}</option>
                @endforeach
            </select>
            <x-field-error name="categoria" />
        </div>
        <div>
            <label for="tiempo" class="field-label">Tiempo en minutos</label>
            <input id="tiempo" name="tiempo" type="number" value="{{ old('tiempo', $receta->tiempo) }}" min="1" max="2147483647" step="1" required placeholder="30" class="field" aria-invalid="{{ $errors->has('tiempo') ? 'true' : 'false' }}" @error('tiempo') aria-describedby="tiempo-error" @enderror>
            <x-field-error name="tiempo" />
        </div>
        <div>
            <label for="dificultad" class="field-label">Dificultad</label>
            <select id="dificultad" name="dificultad" required class="field" aria-invalid="{{ $errors->has('dificultad') ? 'true' : 'false' }}" @error('dificultad') aria-describedby="dificultad-error" @enderror>
                <option value="">Selecciona</option>
                @foreach (\App\Models\Receta::DIFICULTADES as $valor => $etiqueta)
                    <option value="{{ $valor }}" @selected(old('dificultad', $receta->dificultad) === $valor)>{{ $etiqueta }}</option>
                @endforeach
            </select>
            <x-field-error name="dificultad" />
        </div>
    </div>
    <div>
        <label for="ingredientes" class="field-label">Ingredientes</label>
        <p id="ingredientes-help" class="mb-2 text-sm text-stone-500">Escribe un ingrediente por línea, con su cantidad.</p>
        <textarea id="ingredientes" name="ingredientes" rows="6" required maxlength="10000" placeholder="4 tortillas&#10;1 taza de salsa verde&#10;100 g de queso" class="field" aria-invalid="{{ $errors->has('ingredientes') ? 'true' : 'false' }}" aria-describedby="ingredientes-help{{ $errors->has('ingredientes') ? ' ingredientes-error' : '' }}">{{ old('ingredientes', $receta->ingredientes) }}</textarea>
        <x-field-error name="ingredientes" />
    </div>
    <div>
        <label for="pasos" class="field-label">Pasos de preparación</label>
        <p id="pasos-help" class="mb-2 text-sm text-stone-500">Escribe un paso por línea. Los mostraremos en orden.</p>
        <textarea id="pasos" name="pasos" rows="7" required maxlength="15000" placeholder="Calentar la salsa en una sartén.&#10;Rellenar las tortillas y cubrir con salsa.&#10;Añadir el queso y servir." class="field" aria-invalid="{{ $errors->has('pasos') ? 'true' : 'false' }}" aria-describedby="pasos-help{{ $errors->has('pasos') ? ' pasos-error' : '' }}">{{ old('pasos', $receta->pasos) }}</textarea>
        <x-field-error name="pasos" />
    </div>
    <div>
        <label for="nota" class="field-label">Nota personal <span class="font-normal text-stone-500">(opcional)</span></label>
        <textarea id="nota" name="nota" rows="3" maxlength="5000" placeholder="Un consejo, una variación o ese detalle que no quieres olvidar…" class="field" aria-invalid="{{ $errors->has('nota') ? 'true' : 'false' }}" @error('nota') aria-describedby="nota-error" @enderror>{{ old('nota', $receta->nota) }}</textarea>
        <x-field-error name="nota" />
    </div>
    <div class="flex flex-wrap items-center gap-3 border-t border-stone-200 pt-6">
        <button type="submit" class="btn btn-primary">{{ $boton }}</button>
        <a href="{{ $receta->exists ? route('recetas.show', $receta) : route('recetas.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
</div>

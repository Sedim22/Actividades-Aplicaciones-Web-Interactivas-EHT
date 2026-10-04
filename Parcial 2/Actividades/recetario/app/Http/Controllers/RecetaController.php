<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecetaRequest;
use App\Models\Receta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RecetaController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:255'],
            'categoria' => ['nullable', Rule::in(array_keys(Receta::CATEGORIAS))],
        ]);

        $buscar = $filtros['buscar'] ?? '';
        $categoria = $filtros['categoria'] ?? '';
        $consulta = $request->user()->recetas();

        if ($buscar !== '') {
            // Tratar % y _ como texto del título, no como comodines de SQL.
            $patron = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], mb_strtolower($buscar));
            $consulta->whereRaw("LOWER(titulo) LIKE ? ESCAPE '!'", ['%'.$patron.'%']);
        }

        if ($categoria !== '') {
            $consulta->where('categoria', $categoria);
        }

        return view('recetas.index', [
            'recetas' => $consulta->latest('id')->paginate(10)->withQueryString(),
            'hayRecetas' => $request->user()->recetas()->exists(),
            'buscar' => $buscar,
            'categoria' => $categoria,
        ]);
    }

    public function create(): View
    {
        return view('recetas.create', ['receta' => new Receta]);
    }

    public function store(RecetaRequest $request): RedirectResponse
    {
        $receta = $request->user()->recetas()->create($request->validated());

        return to_route('recetas.show', $receta)->with('success', 'Receta creada correctamente.');
    }

    public function show(Request $request, string $receta): View
    {
        return view('recetas.show', [
            'receta' => $request->user()->recetas()->findOrFail($receta),
        ]);
    }

    public function edit(Request $request, string $receta): View
    {
        return view('recetas.edit', [
            'receta' => $request->user()->recetas()->findOrFail($receta),
        ]);
    }

    public function update(RecetaRequest $request, string $receta): RedirectResponse
    {
        $receta = $request->user()->recetas()->findOrFail($receta);
        $receta->update($request->validated());

        return to_route('recetas.show', $receta)->with('success', 'Receta actualizada correctamente.');
    }

    public function destroy(Request $request, string $receta): RedirectResponse
    {
        $request->user()->recetas()->findOrFail($receta)->delete();

        return to_route('recetas.index')->with('success', 'Receta eliminada correctamente.');
    }
}

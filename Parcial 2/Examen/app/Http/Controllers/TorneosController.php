<?php

namespace App\Http\Controllers;

use App\Http\Requests\TorneoRequest;
use App\Models\Torneos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TorneosController extends Controller
{
    public function index(): View
    {
        $torneos = Torneos::withCount('inscripciones')
            ->orderBy('fecha')
            ->orderBy('id')
            ->paginate(10);

        return view('admin.torneos.index', compact('torneos'));
    }

    public function create(): View
    {
        $torneo = new Torneos(['cupo' => 16, 'estado' => 'abierto']);

        return view('admin.torneos.create', compact('torneo'));
    }

    public function store(TorneoRequest $request): RedirectResponse
    {
        Torneos::create($request->validated());

        return redirect()->route('admin.torneos.index')
            ->with('success', 'El torneo se creó correctamente.');
    }

    public function edit(Torneos $torneo): View
    {
        $torneo->loadCount('inscripciones');

        return view('admin.torneos.edit', compact('torneo'));
    }

    public function update(TorneoRequest $request, Torneos $torneo): RedirectResponse
    {
        $datos = $request->validated();

        DB::transaction(function () use ($torneo, $datos): void {
            // Se vuelve a consultar dentro de la transacción para validar el cupo actual.
            $torneoActual = Torneos::whereKey($torneo->id)->lockForUpdate()->firstOrFail();
            $inscritos = $torneoActual->inscripciones()->count();

            if ((int) $datos['cupo'] < $inscritos) {
                throw ValidationException::withMessages([
                    'cupo' => "No puedes reducir el cupo a menos de {$inscritos}: ya hay {$inscritos} jugadores inscritos.",
                ]);
            }

            $torneoActual->update($datos);
        });

        return redirect()->route('admin.torneos.index')
            ->with('success', 'El torneo se actualizó correctamente.');
    }

    public function destroy(Torneos $torneo): RedirectResponse
    {
        // La llave foránea elimina también las inscripciones de este torneo.
        $torneo->delete();

        return redirect()->route('admin.torneos.index')
            ->with('success', 'El torneo y sus inscripciones se eliminaron correctamente.');
    }
}

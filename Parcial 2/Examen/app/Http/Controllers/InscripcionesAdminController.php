<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\Torneos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InscripcionesAdminController extends Controller
{
    public function index(Torneos $torneo): View
    {
        $torneo->loadCount('inscripciones');
        $inscripciones = $torneo->inscripciones()
            ->with('user:id,name,email')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        return view('admin.inscripciones.index', compact('torneo', 'inscripciones'));
    }

    public function destroy(Torneos $torneo, Inscripcion $inscripcion): RedirectResponse
    {
        DB::transaction(function () use ($torneo, $inscripcion): void {
            $actual = Torneos::whereKey($torneo->id)->lockForUpdate()->firstOrFail();

            // Evita dar de baja una inscripción que pertenece a otro torneo.
            $actual->inscripciones()->whereKey($inscripcion->id)->firstOrFail()->delete();
        }, 3);

        return redirect()->route('admin.torneos.inscripciones.index', $torneo)
            ->with('success', 'La inscripción se dio de baja correctamente. La plaza quedó libre.');
    }
}

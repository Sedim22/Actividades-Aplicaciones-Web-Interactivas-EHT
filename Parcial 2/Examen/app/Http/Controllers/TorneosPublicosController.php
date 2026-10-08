<?php

namespace App\Http\Controllers;

use App\Models\Torneos;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TorneosPublicosController extends Controller
{
    public function index(): View
    {
        $torneos = Torneos::disponibles()
            ->withCount('inscripciones')
            ->orderBy('fecha')
            ->orderBy('id')
            ->paginate(12);

        return view('torneos.index', compact('torneos'));
    }

    public function show(Request $request, Torneos $torneo): View
    {
        // El detalle es consultable aunque el torneo ya no esté disponible.
        $torneo->loadCount('inscripciones');
        $participantes = $torneo->inscripciones()
            ->with('user:id,name')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $inscrito = $request->user() !== null
            && $participantes->contains('user_id', $request->user()->id);

        return view('torneos.show', compact('torneo', 'participantes', 'inscrito'));
    }
}

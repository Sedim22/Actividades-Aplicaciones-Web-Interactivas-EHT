<?php

namespace App\Http\Controllers;

use App\Models\Torneos;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InscripcionesController extends Controller
{
    public function index(Request $request): View
    {
        $torneos = Torneos::whereHas('inscripciones', function (Builder $query) use ($request): void {
            $query->where('user_id', $request->user()->id);
        })
            ->withCount('inscripciones')
            ->orderBy('fecha')
            ->orderBy('id')
            ->paginate(10);

        return view('inscripciones.index', compact('torneos'));
    }

    public function store(Request $request, Torneos $torneo): RedirectResponse
    {
        try {
            $error = DB::transaction(function () use ($request, $torneo): ?string {
                // Todas las operaciones sobre las plazas bloquean primero el torneo.
                $actual = Torneos::whereKey($torneo->id)->lockForUpdate()->firstOrFail();

                if ($actual->inscripciones()->where('user_id', $request->user()->id)->exists()) {
                    return 'Ya estás inscrito en este torneo.';
                }

                $aviso = match ($actual->estadoActual()) {
                    'cerrado' => 'No puedes inscribirte: el torneo está cerrado.',
                    'vencido' => 'No puedes inscribirte: la fecha del torneo ya llegó.',
                    'lleno' => 'No puedes inscribirte: el torneo está lleno.',
                    default => null,
                };

                if ($aviso !== null) {
                    return $aviso;
                }

                // Se usa el usuario autenticado, sin aceptar user_id del formulario.
                $actual->inscripciones()->create(['user_id' => $request->user()->id]);

                return null;
            }, 3);
        } catch (UniqueConstraintViolationException) {
            $error = 'Ya estás inscrito en este torneo.';
        }

        if ($error !== null) {
            return redirect()->route('torneos.show', $torneo)->with('error', $error);
        }

        return redirect()->route('torneos.show', $torneo)
            ->with('success', 'Te inscribiste correctamente en el torneo.');
    }

    public function destroy(Request $request, Torneos $torneo): RedirectResponse
    {
        $error = DB::transaction(function () use ($request, $torneo): ?string {
            $actual = Torneos::whereKey($torneo->id)->lockForUpdate()->firstOrFail();
            $inscripcion = $actual->inscripciones()->where('user_id', $request->user()->id)->first();

            if (! $inscripcion) {
                return 'No tienes una inscripción en este torneo.';
            }

            if ($actual->estaVencida()) {
                return 'Solo puedes cancelar tu inscripción antes de la fecha del torneo.';
            }

            $inscripcion->delete();

            return null;
        }, 3);

        if ($error !== null) {
            return redirect()->route('inscripciones.index')->with('error', $error);
        }

        return redirect()->route('inscripciones.index')
            ->with('success', 'Cancelaste tu inscripción. La plaza quedó libre.');
    }
}

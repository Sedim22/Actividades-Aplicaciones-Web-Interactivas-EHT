<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receta extends Model
{
    protected $fillable = [
        'titulo',
        'categoria',
        'tiempo',
        'dificultad',
        'ingredientes',
        'pasos',
        'nota',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'tiempo' => 'integer',
    ];

    //
    public const CATEGORIAS = [
        'desayuno' => 'Desayuno',
        'almuerzo' => 'Almuerzo',
        'cena' => 'Cena',
        'postre' => 'Postre',
        'bebida' => 'Bebida',
    ];

    public const DIFICULTADES = [
        'facil' => 'Fácil',
        'media' => 'Media',
        'dificil' => 'Difícil',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return list<string> */
    public function ingredientesComoLista(): array
    {
        return $this->separarLineas($this->ingredientes);
    }

    /** @return list<string> */
    public function pasosComoLista(): array
    {
        return $this->separarLineas($this->pasos);
    }

    private function separarLineas(string $texto): array
    {
        return array_values(array_filter(
            array_map('trim', preg_split('/\R/u', $texto)),
            fn (string $linea) => $linea !== '',
        ));
    }
}

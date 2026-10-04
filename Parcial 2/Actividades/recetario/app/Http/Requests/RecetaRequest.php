<?php

namespace App\Http\Requests;

use App\Models\Receta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'categoria' => ['required', Rule::in(array_keys(Receta::CATEGORIAS))],
            'tiempo' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'dificultad' => ['required', Rule::in(array_keys(Receta::DIFICULTADES))],
            'ingredientes' => ['required', 'string', 'max:10000'],
            'pasos' => ['required', 'string', 'max:15000'],
            'nota' => ['nullable', 'string', 'max:5000'],
        ];
    }
}

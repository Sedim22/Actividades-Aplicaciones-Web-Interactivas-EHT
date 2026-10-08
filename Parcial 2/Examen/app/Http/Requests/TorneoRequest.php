<?php

namespace App\Http\Requests;

use App\Models\Torneos;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TorneoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // El acceso de administrador se comprueba con middleware en web.php.
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->isMethod('post')) {
            $this->mergeIfMissing(['cupo' => 16]);
        }
    }

    public function rules(): array
    {
        return [
            'nombre_torneo' => ['bail', 'required', 'string', 'max:255'],
            'tipo' => ['bail', 'required', 'string', Rule::in(array_keys(Torneos::TIPOS))],
            'fecha' => ['bail', 'required', 'date_format:Y-m-d', 'after:today'],
            'cupo' => ['bail', 'required', 'integer', 'min:2', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:10000'],
            'estado' => ['bail', 'required', 'string', Rule::in(array_keys(Torneos::ESTADOS))],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_torneo.required' => 'El nombre del torneo es obligatorio.',
            'nombre_torneo.string' => 'El nombre del torneo debe ser texto.',
            'nombre_torneo.max' => 'El nombre no debe superar los 255 caracteres.',
            'tipo.required' => 'Selecciona un juego o deporte.',
            'tipo.string' => 'Selecciona un juego o deporte válido.',
            'tipo.in' => 'Selecciona uno de los juegos o deportes disponibles.',
            'fecha.required' => 'La fecha del torneo es obligatoria.',
            'fecha.date_format' => 'Ingresa una fecha válida con el formato año-mes-día.',
            'fecha.after' => 'La fecha del torneo debe ser posterior a hoy.',
            'cupo.required' => 'El cupo es obligatorio.',
            'cupo.integer' => 'El cupo debe ser un número entero.',
            'cupo.min' => 'El cupo debe ser de al menos 2 jugadores.',
            'cupo.max' => 'El cupo no puede superar los 100 jugadores.',
            'descripcion.string' => 'La descripción debe ser texto.',
            'descripcion.max' => 'La descripción no debe superar los 10,000 caracteres.',
            'estado.required' => 'Selecciona el estado del torneo.',
            'estado.string' => 'Selecciona un estado válido.',
            'estado.in' => 'El estado debe ser abierto o cerrado.',
        ];
    }
}

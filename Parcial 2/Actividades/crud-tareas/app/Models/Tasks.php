<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    protected  $fillable = [
        'titulo',
        'descripcion',
        'estado',
        'prioridad',
        'vencimiento',
    ];

    protected $casts = [
        'vencimiento'=> 'date',
    ];

    public const ESTADOS = [
        'por_hacer' => 'Por hacer',
        'en_curso' => 'En curso',
        'hecha' => 'Hecha',
    ];

    public const PRIORIDADES = [
        'baja' => 'Baja',
        'media' => 'Media',
        'alta' => 'Alta',
    ];

    public function estaVencida(): bool
    {
        return $this->vencimiento
            && $this->vencimiento->lt(today())
            && $this->estado !== 'hecha';
    }
}

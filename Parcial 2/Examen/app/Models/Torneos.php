<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Torneos extends Model
{
    protected $table = 'torneos';

    protected $fillable = [
        'nombre_torneo',
        'descripcion',
        'tipo',
        'estado',
        'cupo',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cupo' => 'integer',
    ];

    public const ESTADOS = [
        'abierto' => 'Abierto',
        'cerrado' => 'Cerrado',
    ];

    public const TIPOS = [
        'videojuegos' => 'Videojuegos',
        'futbol' => 'Fútbol',
        'basquetbol' => 'Basquetbol',
        'tennis' => 'Tenis',
        'beisbol' => 'Béisbol',
        'ping_pong' => 'Ping-pong',
    ];

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'torneo_id');
    }

    public function estaVencida(): bool
    {
        return $this->fecha
            && $this->fecha->lte(today());
    }

    public function scopeDisponibles(Builder $query): Builder
    {
        return $query->where('estado', 'abierto')
            ->whereDate('fecha', '>', today())
            ->where('cupo', '>', Inscripcion::selectRaw('COUNT(*)')
                ->whereColumn('inscripciones.torneo_id', 'torneos.id'));
    }

    public function plazasDisponibles(): int
    {
        $inscritos = $this->inscripciones_count ?? $this->inscripciones()->count();

        return max(0, $this->cupo - $inscritos);
    }

    public function estadoActual(): string
    {
        if ($this->estado !== 'abierto') {
            return 'cerrado';
        }

        if ($this->estaVencida()) {
            return 'vencido';
        }

        return $this->plazasDisponibles() === 0 ? 'lleno' : 'abierto';
    }
}

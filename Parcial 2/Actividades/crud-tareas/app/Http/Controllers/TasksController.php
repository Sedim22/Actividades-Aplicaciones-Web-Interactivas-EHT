<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use Illuminate\Http\Request;

class TasksController extends Controller
{
       // Muestra el tablero con las tareas agrupadas por estado.
    // Acepta filtros por estado y prioridad, y búsqueda por título.
    public function index(Request $peticion)
    {
        $consulta = Tasks::query();

        // Solo filtra por valores válidos; ignora el resto.
        if (array_key_exists($peticion->query('estado'), Tasks::ESTADOS)) {
            $consulta->where('estado', $peticion->query('estado'));
        }

        if (array_key_exists($peticion->query('prioridad'), Tasks::PRIORIDADES)) {
            $consulta->where('prioridad', $peticion->query('prioridad'));
        }

        if ($peticion->filled('q')) {
            $consulta->where('titulo', 'like', '%'.$peticion->query('q').'%');
        }

        $tareas = $consulta->orderByDesc('created_at')->get()->groupBy('estado');

        return view('tasks.index', [
            'tareasPorEstado' => $tareas,
            'estados' => Tasks::ESTADOS,
            'prioridades' => Tasks::PRIORIDADES,
            'filtros' => $peticion->only(['estado', 'prioridad', 'q']),
        ]);
    }

    // Muestra el formulario para crear una tarea.
    public function create()
    {
        return view('tasks.create', [
            'estados' => Tasks::ESTADOS,
            'prioridades' => Tasks::PRIORIDADES,
        ]);
    }

    // Guarda la tarea nueva y vuelve al tablero.
    public function store(Request $peticion)
    {
        $datos = $this->validar($peticion);

        Tasks::create($datos);

        return redirect()->route('tasks.index')->with('exito', 'Tarea creada.');
    }

    // Muestra el detalle de una tarea.
    public function show(Tasks $task)
    {
        return view('tasks.show', [
            'tarea' => $task,
            'estados' => Tasks::ESTADOS,
            'prioridades' => Tasks::PRIORIDADES,
        ]);
    }

    // Muestra el formulario para editar una tarea.
    public function edit(Tasks $task)
    {
        return view('tasks.edit', [
            'tarea' => $task,
            'estados' => Tasks::ESTADOS,
            'prioridades' => Tasks::PRIORIDADES,
        ]);
    }

    // Guarda los cambios de la tarea y vuelve al tablero.
    public function update(Request $peticion, Tasks $task)
    {
        $datos = $this->validar($peticion);

        $task->update($datos);

        return redirect()->route('tasks.index')->with('exito', 'Tarea actualizada.');
    }

    // Elimina la tarea definitivamente y vuelve al tablero.
    public function destroy(Tasks $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('exito', 'Tarea eliminada.');
    }

    // Cambia solo el estado desde los botones de la tarjeta.
    public function changeStatus(Request $peticion, Tasks $task)
    {
        $datos = $peticion->validate(
            ['estado' => 'required|in:por_hacer,en_curso,hecha'],
            ['estado.required' => 'El estado es obligatorio.', 'estado.in' => 'El estado no es válido.']
        );

        $task->update($datos);

        return redirect()->route('tasks.index')->with('exito', 'Estado actualizado.');
    }

    // Reglas de validación comunes a crear y editar, con mensajes en español.
    protected function validar(Request $peticion): array
    {
        return $peticion->validate(
            [
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:2000',
                'estado' => 'required|in:por_hacer,en_curso,hecha',
                'prioridad' => 'required|in:baja,media,alta',
                'vencimiento' => 'nullable|date',
            ],
            [
                'titulo.required' => 'El título es obligatorio.',
                'titulo.max' => 'El título no puede tener más de 255 caracteres.',
                'descripcion.max' => 'La descripción no puede tener más de 2000 caracteres.',
                'estado.required' => 'El estado es obligatorio.',
                'estado.in' => 'El estado no es válido.',
                'prioridad.required' => 'La prioridad es obligatoria.',
                'prioridad.in' => 'La prioridad no es válida.',
                'vencimiento.date' => 'La fecha de vencimiento no es válida.',
            ]
        );
    }
}

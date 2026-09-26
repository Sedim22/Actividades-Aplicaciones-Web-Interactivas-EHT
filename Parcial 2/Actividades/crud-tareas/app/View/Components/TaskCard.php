<?php

namespace App\View\Components;

use App\Models\Tasks;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TaskCard extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public Tasks $tarea,
        public array $prioridades,
        public array $estados,
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.task-card');
    }
}

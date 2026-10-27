<?php

namespace App\Livewire;

use Livewire\Component;

class ExperienceCard extends Component
{
    public string $role = '';

    public string $date = '';

    public string $description = '';

    public function mount(string $role, string $date, string $description): void
    {
        $this->role = $role;
        $this->date = $date;
        $this->description = $description;
    }

    public function render()
    {
        return view('livewire.experience-card');
    }
}

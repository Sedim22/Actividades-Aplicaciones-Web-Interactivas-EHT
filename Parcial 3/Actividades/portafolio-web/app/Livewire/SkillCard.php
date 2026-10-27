<?php

namespace App\Livewire;

use Livewire\Component;

class SkillCard extends Component
{
    public string $name = '';

    public ?string $image = null;

    public function mount(string $name, ?string $image = null): void
    {
        $this->name = $name;
        $this->image = $image;
    }

    public function render()
    {
        return view('livewire.skill-card');
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;

class ProjectCard extends Component
{
    public string $name = '';

    public ?string $image = null;

    /** @var array<string>|string */
    public array|string $technologies = [];

    public string $description = '';

    public ?string $url = null;

    /**
     * @param  array<string>|string  $technologies
     */
    public function mount(
        string $name,
        string $description,
        array|string $technologies = [],
        ?string $image = null,
        ?string $url = null
    ): void {
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;
        $this->url = $url;

        if (is_string($technologies)) {
            $this->technologies = array_filter(array_map('trim', explode(',', $technologies)));
        } else {
            $this->technologies = $technologies;
        }
    }

    public function render()
    {
        return view('livewire.project-card');
    }
}

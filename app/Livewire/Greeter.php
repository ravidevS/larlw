<?php

namespace App\Livewire;

use App\Models\Greeting;
use Livewire\Component;

class Greeter extends Component
{
    public string $name = '';

    public string $greeting = '';

    /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Greeting> */
    public $greetings;

    public string $greetingMessage = '';

    public function changeGreeting(): void
    {
        $this->greetingMessage = "{$this->greeting}, {$this->name}";
    }

    public function mount(): void
    {
        $this->greetings = Greeting::all();

        if ($this->greetings->isNotEmpty()) {
            $this->greeting = $this->greetings->first()?->greeting ?? '';
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.greeter');
    }
}

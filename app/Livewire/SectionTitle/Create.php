<?php

namespace App\Livewire\SectionTitle;

use Livewire\Component;
use App\Models\SectionTitle;

class Create extends Component
{
    public $title = '';

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
        ]);

        SectionTitle::create([
            'title' => $this->title,
        ]);

        return redirect()->route('section-title.index');
    }

    public function render()
    {
        return view('livewire.section-title.create');
    }
}

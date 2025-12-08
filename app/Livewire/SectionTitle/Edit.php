<?php

namespace App\Livewire\SectionTitle;

use Livewire\Component;
use App\Models\SectionTitle;

class Edit extends Component
{
    public SectionTitle $sectionTitle;
    public $title;

    public function mount(SectionTitle $sectionTitle)
    {
        $this->sectionTitle = $sectionTitle;
        $this->title = $sectionTitle->title;
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
        ]);

        $this->sectionTitle->update([
            'title' => $this->title,
        ]);

        return redirect()->route('section-title.index');
    }

    public function render()
    {
        return view('livewire.section-title.edit');
    }
}

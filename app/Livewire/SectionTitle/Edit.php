<?php

namespace App\Livewire\SectionTitle;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\SectionTitle;
use App\Traits\HandlesIconUpload;

class Edit extends Component
{
    use WithFileUploads, HandlesIconUpload;

    public SectionTitle $sectionTitle;
    public $title;
    public $icon;

    public function mount(SectionTitle $sectionTitle)
    {
        $this->sectionTitle = $sectionTitle;
        $this->title = $sectionTitle->title;
        $this->icon = null; // new upload is optional
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|image|max:2048',
        ]);

        $filename = $this->saveIcon($this->icon, $this->sectionTitle->icon);

        $this->sectionTitle->update([
            'title' => $this->title,
            'icon' => $filename,
        ]);

        return redirect()->route('section-title.index');
    }

    public function render()
    {
        return view('livewire.section-title.edit');
    }
}

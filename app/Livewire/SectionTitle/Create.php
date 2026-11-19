<?php

namespace App\Livewire\SectionTitle;

use App\Models\SectionTitle;
use App\Traits\HandlesIconUpload;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use HandlesIconUpload, WithFileUploads;

    public $title = '';

    public $icon;

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'icon' => ['required', ...$this->iconRules()],
        ]);

        $nextSortOrder = SectionTitle::max('sort_order') + 1;

        $filename = $this->saveIcon($this->icon); // reuse trait

        SectionTitle::create([
            'title' => $this->title,
            'icon' => $filename,
            'sort_order' => $nextSortOrder,
        ]);

        return redirect()->route('section-title.index');
    }

    public function render()
    {
        return view('livewire.section-title.create');
    }
}

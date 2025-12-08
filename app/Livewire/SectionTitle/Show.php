<?php

namespace App\Livewire\SectionTitle;

use Livewire\Component;
use App\Models\SectionTitle;

class Show extends Component
{
    public SectionTitle $sectionTitle;

    public function mount(SectionTitle $sectionTitle)
    {
        $this->sectionTitle = $sectionTitle;
    }

    public function render()
    {
        return view('livewire.section-title.show');
    }
}

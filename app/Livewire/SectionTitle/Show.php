<?php

namespace App\Livewire\SectionTitle;

use App\Models\SectionTitle;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public SectionTitle $sectionTitle;

    public function mount(SectionTitle $sectionTitle): void
    {
        $this->sectionTitle = $sectionTitle;
    }

    public function render(): View
    {
        return view('livewire.section-title.show');
    }
}

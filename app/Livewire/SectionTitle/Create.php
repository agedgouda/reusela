<?php

namespace App\Livewire\SectionTitle;

use App\Models\SectionTitle;
use App\Traits\HandlesIconUpload;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Create extends Component
{
    use HandlesIconUpload, WithFileUploads;

    public string $title = '';

    public ?TemporaryUploadedFile $icon = null;

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'icon' => ['required', ...$this->iconRules()],
        ]);

        $highestSortOrder = SectionTitle::max('sort_order');
        $nextSortOrder = (is_numeric($highestSortOrder) ? (int) $highestSortOrder : 0) + 1;

        $filename = $this->saveIcon($this->icon); // reuse trait

        SectionTitle::create([
            'title' => $this->title,
            'icon' => $filename,
            'sort_order' => $nextSortOrder,
        ]);

        $this->redirectRoute('section-title.index');
    }

    public function render(): View
    {
        return view('livewire.section-title.create');
    }
}

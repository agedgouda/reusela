<?php

namespace App\Livewire\SectionTitle;

use App\Models\SectionTitle;
use App\Traits\HandlesIconUpload;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use HandlesIconUpload, WithFileUploads;

    public SectionTitle $sectionTitle;

    public string $title = '';

    public ?TemporaryUploadedFile $icon = null;

    public function mount(SectionTitle $sectionTitle): void
    {
        $this->sectionTitle = $sectionTitle;
        $this->title = $sectionTitle->title;
        $this->icon = null; // new upload is optional
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'icon' => ['nullable', ...$this->iconRules()],
        ]);

        $filename = $this->saveIcon($this->icon, $this->sectionTitle->icon);

        $this->sectionTitle->update([
            'title' => $this->title,
            'icon' => $filename,
        ]);

        $this->redirectRoute('section-title.index');
    }

    public function render(): View
    {
        return view('livewire.section-title.edit');
    }
}

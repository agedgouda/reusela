<?php

namespace App\Livewire\Jurisdiction;

use Livewire\Component;
use App\Models\Section;
use App\Models\SectionTitle;

class EditSection extends Component
{
    public string $jurisdictionId;
    public Section $section;
    public ?string $newSectionTitleId = null;
    public ?string $newSectionText = null;

    public $availableSectionTitles = [];

    public function mount($jurisdictionId, ?Section $section = null)
    {
        $this->jurisdictionId = $jurisdictionId;
        $this->section = $section ?? new Section;

        // Always load related title
        $this->section->load('sectionTitle');

        // If editing, load section values into form
        if ($this->section->exists) {
            $this->newSectionTitleId = $this->section->section_title_id;
            $this->newSectionText = $this->section->text;
        }

       $this->refreshAvailableTitles();

    }

    public function cancel(): void
    {
         $this->closeEditor();
    }

    public function save($addNew = false)
    {
        if ($this->section->exists) {
            // Editing an existing section
            $this->section->update([
                'text' => $this->newSectionText,
            ]);
        } else {
            // Adding a new section
            $this->validate([
                'newSectionTitleId' => 'required|exists:section_titles,id',
                'newSectionText' => 'required|string',
            ]);

            Section::create([
                'jurisdiction_id' => $this->jurisdictionId,
                'section_title_id' => $this->newSectionTitleId,
                'text' => $this->newSectionText,
            ]);
        }

        // Single unified event dispatch
        $this->dispatch('section-added');

        if (! $addNew) {
            $this->closeEditor();
        } else {
            // Reset local state for "Add New"
            $this->section = new Section;
            $this->newSectionTitleId = null;
            $this->newSectionText = '';

            $this->resetErrorBag();

            // Refresh the titles list so the one just used is removed
            $this->refreshAvailableTitles();
        }
    }


    public function render()
    {
        return view('livewire.jurisdiction.edit-section');
    }

    protected function closeEditor(): void
    {
        if ($this->section->exists) {
            $this->dispatch('toggleEdit', ['sectionId' => $this->section->id]);
        } else {
            $this->dispatch('cancelAddSection');
        }
    }

    protected function refreshAvailableTitles()
    {
        $usedTitleIds = Section::where('jurisdiction_id', $this->jurisdictionId)
            ->pluck('section_title_id')
            ->toArray();

        $this->availableSectionTitles = SectionTitle::whereNotIn('id', $usedTitleIds)
            ->orderBy('sort_order')
            ->get();
    }

}

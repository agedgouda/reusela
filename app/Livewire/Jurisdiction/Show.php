<?php

namespace App\Livewire\Jurisdiction;

use Livewire\Component;
use App\Livewire\Concerns\HandlesSectionDeletions;
use App\Models\Jurisdiction;
use App\Models\SectionTitle;
use App\Models\Section;
use Illuminate\Support\Collection;

class Show extends Component
{
    use HandlesSectionDeletions;

    public Jurisdiction $jurisdiction;
    public string $jurisdictionId;
    public bool $showAddSectionForm = false;
    public bool $showGeneralInfoEdit = false;
    public bool $editable = true;
    public bool $showAddSectionButton = true;
    public Collection $defaultSections;

    // Default tab state
    public string $tab = 'list';

    /** @var int|null */
    public $editingSectionId = null;

    protected $listeners = [
        'sectionAdded'     => 'refreshSections',
        'sectionUpdated'   => 'refreshSections',
        'cancelAddSection' => 'hideAddSectionForm',
        'toggleEdit'
    ];

    public function mount(Jurisdiction $jurisdiction, ?bool $editable = null)
    {
        $this->jurisdiction   = $jurisdiction;
        $this->jurisdictionId = $jurisdiction->id;

        if (! is_null($editable)) {
            $this->editable = $editable;
        }

        $this->updateButtonVisibility();
        $this->loadSections();
    }

    /**
     * This method is called automatically by Livewire
     * when the 'tab' property is updated via wire:model.
     */
    public function updatedTab($value)
    {
        // Reset editing states when switching tabs to prevent UI bugs
        $this->showAddSectionForm = false;
        $this->editingSectionId = null;
    }

    public function deleteSection($id)
    {
        $this->performDelete($id, Section::class, 'Section');
    }

    public function toggleEdit($target): void
    {
        if ($target === 'general') {
            $this->showGeneralInfoEdit = !$this->showGeneralInfoEdit;
            $this->editingSectionId = null;
        } else {
            $this->editingSectionId = ($this->editingSectionId == $target) ? null : $target;
            $this->showGeneralInfoEdit = false;
        }
    }

    public function hideAddSectionForm()
    {
        $this->showAddSectionForm = false;
        $this->editingSectionId = null;
    }

    private function loadSections()
    {
        $this->jurisdiction->load('sections.sectionTitle');
    }

    public function refreshSections($keepAdding = false)
    {
        $this->editingSectionId = null;
        $this->showAddSectionForm = $keepAdding;

        $this->loadSections();
        $this->updateButtonVisibility();
    }

    private function updateButtonVisibility()
    {
        $this->showAddSectionButton = SectionTitle::count() !== $this->jurisdiction->sections->count();
    }

    public function render()
    {
        return view('livewire.jurisdiction.show');
    }
}

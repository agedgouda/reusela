<?php

namespace App\Livewire\Jurisdiction;

use App\Livewire\Concerns\HandlesSectionDeletions;
use App\Livewire\Concerns\LoadsSections;
use App\Livewire\Concerns\ManagesSectionUI;
use App\Models\Jurisdiction;
use App\Models\Section;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Show extends Component
{
    // Use the three pillars of our refactored logic
    use HandlesSectionDeletions,
        LoadsSections,
        ManagesSectionUI;

    public Jurisdiction $jurisdiction;

    public string $jurisdictionId;

    public bool $showAddSectionForm = false;

    public bool $showGeneralInfoEdit = false;

    public bool $editable = true;

    public bool $showAddSectionButton = true;

    public bool $editingName = false;

    public string $jurisdictionName = '';

    public string $tab = 'list';

    public $editingSectionId = null;

    protected $listeners = [
        'sectionAdded' => 'refreshSections',
        'sectionUpdated' => 'refreshSections',
        'cancelAddSection' => 'closeEditor', // Matches the trait method
        'toggleEdit',
    ];

    public function mount(Jurisdiction $jurisdiction, ?bool $editable = null): void
    {
        $this->jurisdiction = $jurisdiction;
        $this->jurisdictionId = $jurisdiction->id;
        $this->jurisdictionName = $jurisdiction->name;

        if (! is_null($editable)) {
            $this->editable = $editable;
        }

        if ($this->editable && $jurisdiction->name === '') {
            $this->editingName = true;
        }

        $this->refreshSections();
    }

    public function saveJurisdictionName(): void
    {
        $this->jurisdictionName = Str::title($this->jurisdictionName);

        $this->validate([
            'jurisdictionName' => ['required', 'string', 'max:255', Rule::unique('jurisdictions', 'name')->ignore($this->jurisdiction->id)],
        ]);

        $this->jurisdiction->update(['name' => $this->jurisdictionName]);
        $this->editingName = false;
    }

    /**
     * Responds to wire:model or manual updates to the tab property
     */
    public function updatedTab($value)
    {
        $this->closeEditor();
    }

    /**
     * Standardized refresh using the LoadsSections trait
     */
    public function refreshSections($keepAdding = false)
    {
        $this->traitRefreshSections(\App\Models\Section::class, $this->jurisdiction->id, 'jurisdiction_id', $keepAdding);
    }

    /**
     * Standardized deletion using the HandlesSectionDeletions trait
     */
    public function deleteSection($id)
    {
        $this->performDelete($id, Section::class, 'Section');
    }

    public function goBack()
    {
        return redirect('/jurisdictions');
    }

    public function render()
    {
        return view('livewire.jurisdiction.show', [
            'generalInfo' => $this->jurisdiction->display_general_info,
            'sectionsToDisplay' => $this->sections,
        ]);
    }
}

<?php

namespace App\Livewire\Jurisdiction;

use Livewire\Component;
use App\Models\Section;
use App\Models\Jurisdiction;
use App\Models\SectionTitle;
use App\Livewire\Concerns\LoadsSections;
use App\Livewire\Concerns\HandlesSectionDeletions;
use App\Livewire\Concerns\ManagesSectionUI;

class Show extends Component
{
    // Use the three pillars of our refactored logic
    use LoadsSections,
        HandlesSectionDeletions,
        ManagesSectionUI;

    public Jurisdiction $jurisdiction;
    public string $jurisdictionId;
    public bool $showAddSectionForm = false;
    public bool $showGeneralInfoEdit = false;
    public bool $editable = true;
    public bool $showAddSectionButton = true;

    public string $tab = 'list';
    public $editingSectionId = null;

    protected $listeners = [
        'sectionAdded'     => 'refreshSections',
        'sectionUpdated'   => 'refreshSections',
        'cancelAddSection' => 'closeEditor', // Matches the trait method
        'toggleEdit'
    ];

    public function mount(Jurisdiction $jurisdiction, ?bool $editable = null)
    {
        $this->jurisdiction   = $jurisdiction;
        $this->jurisdictionId = $jurisdiction->id;

        if (! is_null($editable)) {
            $this->editable = $editable;
        }

        $this->refreshSections();
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
       $this->traitRefreshSections(\App\Models\Section::class,$this->jurisdiction->id,'jurisdiction_id',$keepAdding);
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
            // Using the accessors we just fixed
            'generalInfo' => $this->jurisdiction->display_general_info,
            'sectionsToDisplay' => $this->jurisdiction->display_sections,
        ]);
    }
}

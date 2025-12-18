<?php

namespace App\Livewire\Jurisdiction;

use Livewire\Component;
use App\Models\Jurisdiction;

class Show extends Component
{
    public Jurisdiction $jurisdiction;

    public string $jurisdictionId;
    public bool $showAddSectionForm = false;
    public bool $showGeneralInfoEdit = false;
    public bool $editable = true;
    public bool $showAddSectionButton = true;

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

        if(\App\Models\SectionTitle::count() === $jurisdiction->sections->count() ) {
            $showAddSectionButton = false;
        }

        $this->loadSections();
    }

    /** ------------------------------------------
     *  UI ACTIONS
     * ----------------------------------------- */

    public function toggleEdit($target): void
    {
        if ($target === 'general') {
            $this->showGeneralInfoEdit = ! $this->showGeneralInfoEdit;
            $this->editingSectionId = false;
        } else {
            // assume $target is a section ID
            $this->editingSectionId = ($this->editingSectionId === $target) ? null : $target;
            $this->showGeneralInfoEdit = false;
        }
    }
    public function hideAddSectionForm()
    {
        $this->showAddSectionForm = false;
    }

    /** ------------------------------------------
     *  DATA LOADING
     * ----------------------------------------- */

    private function loadSections()
    {
        // Eager load everything needed for the view
        $this->jurisdiction->load('sections.sectionTitle');
    }

    public function refreshSections()
    {
        $this->loadSections();
    }

    /** ------------------------------------------
     *  RENDER
     * ----------------------------------------- */

    public function render()
    {
        return view('livewire.jurisdiction.show', [
            'jurisdiction' => $this->jurisdiction,
        ]);
    }
}

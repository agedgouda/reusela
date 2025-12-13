<?php

namespace App\Livewire\Jurisdiction;

use Livewire\Component;
use App\Models\Jurisdiction;

class Show extends Component
{
    public Jurisdiction $jurisdiction;

    public string $jurisdictionId;
    public bool $showAddSectionForm = false;
    public bool $editable = true;

    /** @var int|null */
    public $editingSectionId = null;

    protected $listeners = [
        'sectionAdded'     => 'refreshSections',
        'sectionUpdated'   => 'refreshSections',
        'cancelAddSection' => 'hideAddSectionForm',
        'toggleEditSection'
    ];

    public function mount(Jurisdiction $jurisdiction, ?bool $editable = null)
    {
        $this->jurisdiction   = $jurisdiction;
        $this->jurisdictionId = $jurisdiction->id;

        if (! is_null($editable)) {
            $this->editable = $editable;
        }

        $this->loadSections();
    }

    /** ------------------------------------------
     *  UI ACTIONS
     * ----------------------------------------- */

    public function toggleEditSection($sectionId)
    {
        // If clicking the same section, close. Otherwise open.
        $this->editingSectionId =
            ($this->editingSectionId === $sectionId) ? null : $sectionId;
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

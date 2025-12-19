<?php

namespace App\Livewire\Jurisdiction;

use Livewire\Component;
use App\Models\DefaultSection;
use App\Models\SectionTitle;
use App\Livewire\Concerns\LoadsSections;
use App\Livewire\Concerns\HandlesSectionDeletions;
use App\Livewire\Concerns\ManagesSectionUI;
use Illuminate\Support\Collection;

class DefaultContent extends Component
{
    use LoadsSections, HandlesSectionDeletions, ManagesSectionUI;

    public $editingSectionId = null;
    public bool $showAddSectionForm = false;
    public bool $editable = true;

    protected $listeners = [
        'sectionAdded' => 'refreshSections',
        'sectionUpdated' => 'refreshSections',
        'cancelAddSection' => 'hideAddSectionForm',
        'toggleEdit'
    ];

    public function mount()
    {
        $this->refreshSections();
    }

    public function refreshSections($keepAdding = false)
    {
        $this->traitRefreshSections(\App\Models\DefaultSection::class,null,'',$keepAdding);
    }

    public function deleteSection($id)
    {
        $this->performDelete($id, DefaultSection::class, 'Default Section');
    }

    public function render()
    {
        return view('livewire.jurisdiction.default-content', [
            'defaultSections' => $this->sections
        ]);
    }
}

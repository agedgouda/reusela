<?php

namespace App\Livewire\Jurisdiction;

use Livewire\Component;
use App\Models\DefaultSection;
use App\Livewire\Concerns\HandlesSectionDeletions;
use Illuminate\Support\Collection;

class DefaultContent extends Component
{
    use HandlesSectionDeletions;

    public Collection $defaultSections;
    public $editingSectionId = null;
    public bool $showAddSectionForm = false;

    protected $listeners = [
        'sectionAdded' => 'refreshSections',
        'sectionUpdated' => 'refreshSections',
        'cancelAddSection' => 'hideAddSectionForm',
        'toggleEdit'
    ];

    public function mount()
    {
        $this->loadSections();
    }

    public function loadSections()
    {
        $this->defaultSections = DefaultSection::with('sectionTitle')
            ->get()
            ->sortBy(fn($d) => $d->sectionTitle->sort_order);
    }

    public function deleteSection($id)
    {
        $this->performDelete($id, DefaultSection::class, 'Default Section');
    }

    public function toggleEdit($target): void
    {
        $this->editingSectionId = ($this->editingSectionId == $target) ? null : $target;
        $this->showAddSectionForm = false;
    }

    public function hideAddSectionForm()
    {
        $this->showAddSectionForm = false;
        $this->editingSectionId = null;
    }

    public function refreshSections($keepAdding = false)
    {
        $this->editingSectionId = null;
        $this->showAddSectionForm = $keepAdding;
        $this->loadSections();
    }

    public function render()
    {
        return view('livewire.jurisdiction.default-content');
    }
}

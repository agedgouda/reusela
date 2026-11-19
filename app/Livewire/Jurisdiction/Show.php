<?php

namespace App\Livewire\Jurisdiction;

use App\Livewire\Concerns\HandlesSectionDeletions;
use App\Livewire\Concerns\InteractsWithAuthenticatedUser;
use App\Livewire\Concerns\LoadsSections;
use App\Livewire\Concerns\ManagesSectionUI;
use App\Models\Jurisdiction;
use App\Models\Section;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Shown to admins on the jurisdiction page and, read-only, to the public on the search page.
 * Every action that changes data must call authorizeEditing().
 */
class Show extends Component
{
    use HandlesSectionDeletions,
        InteractsWithAuthenticatedUser,
        LoadsSections,
        ManagesSectionUI;

    public Jurisdiction $jurisdiction;

    #[Locked]
    public string $jurisdictionId;

    public bool $showAddSectionForm = false;

    public bool $showGeneralInfoEdit = false;

    #[Locked]
    public bool $editable = true;

    public bool $editingName = false;

    public string $jurisdictionName = '';

    public string $tab = 'list';

    public int|string|null $editingSectionId = null;

    /**
     * @var array<int|string, string>
     */
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
        $this->authorizeEditing();

        $this->jurisdictionName = Str::title($this->jurisdictionName);

        $this->validate([
            'jurisdictionName' => ['required', 'string', 'max:255', Rule::unique('jurisdictions', 'name')->ignore($this->jurisdiction->id)],
        ]);

        $this->jurisdiction->update(['name' => $this->jurisdictionName]);
        $this->editingName = false;
    }

    /**
     * Responds to wire:model or manual updates to the tab property.
     */
    public function updatedTab(): void
    {
        $this->closeEditor();
    }

    /**
     * Standardized refresh using the LoadsSections trait.
     */
    public function refreshSections(bool $keepAdding = false): void
    {
        $this->traitRefreshSections(Section::class, $this->jurisdiction->id, 'jurisdiction_id', $keepAdding);
    }

    /**
     * Standardized deletion using the HandlesSectionDeletions trait.
     */
    public function deleteSection(int|string $id): void
    {
        $this->authorizeEditing();

        $this->performDelete($id, Section::class, 'Section');
    }

    public function goBack(): void
    {
        $this->redirectRoute('jurisdiction.index');
    }

    public function render(): View
    {
        return view('livewire.jurisdiction.show', [
            'generalInfo' => $this->jurisdiction->display_general_info,
            'sectionsToDisplay' => $this->sections,
        ]);
    }
}

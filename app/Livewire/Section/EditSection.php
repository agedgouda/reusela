<?php

namespace App\Livewire\Section;

use App\Livewire\Concerns\HandlesSectionSaving;
use App\Livewire\Concerns\InteractsWithAuthenticatedUser;
use App\Models\Jurisdiction;
use App\Models\Section;
use App\Models\SectionTitle;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class EditSection extends Component
{
    use HandlesSectionSaving, InteractsWithAuthenticatedUser;

    #[Locked]
    public Section $model;

    #[Locked]
    public ?Jurisdiction $parentModel = null;

    #[Locked]
    public string $foreignKey = 'jurisdiction_id';

    public int|string|null $newSectionTitleId = null;

    public ?string $newSectionText = null;

    public function mount(Section $model, ?Jurisdiction $parentModel = null, string $foreignKey = 'jurisdiction_id'): void
    {
        $this->authorizeEditing();

        $this->model = $model;
        $this->parentModel = $parentModel;
        $this->foreignKey = $foreignKey;

        if ($this->model->exists) {
            $this->newSectionTitleId = $this->model->section_title_id;
            $this->newSectionText = $this->model->text;
        }
    }

    public function save(bool $addNew = false): void
    {
        $this->authorizeEditing();

        $savedModel = $this->performSave(
            $this->model,
            ['section_title_id' => $this->newSectionTitleId, 'text' => $this->newSectionText],
            $this->parentModel,
            $this->foreignKey,
            $addNew
        );

        if ($savedModel instanceof Section) {
            $this->model = $savedModel;
        }

        if ($addNew) {
            $this->resetForm();
        }
    }

    public function resetForm(): void
    {
        $this->model = new Section;
        $this->newSectionTitleId = null;
        $this->newSectionText = '';
    }

    public function closeEditor(): void
    {
        // Tell the parent component to hide this form
        $this->dispatch('cancelAddSection');
    }

    public function render(): View
    {
        $usedTitleIds = Section::query()
            ->when($this->model->exists, fn ($query) => $query->where('id', '!=', $this->model->id))
            ->when($this->parentModel, fn ($query, Jurisdiction $parent) => $query->where($this->foreignKey, $parent->id))
            ->pluck('section_title_id');

        return view('livewire.section.edit-section', [
            'availableSectionTitles' => SectionTitle::whereNotIn('id', $usedTitleIds)->orderBy('sort_order')->get(),
        ]);
    }
}

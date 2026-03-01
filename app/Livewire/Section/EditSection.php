<?php

namespace App\Livewire\Section;

use Livewire\Component;
use App\Models\SectionTitle;
use App\Livewire\Concerns\HandlesSectionSaving;

class EditSection extends Component
{
    use HandlesSectionSaving;

    public $model;
    public $parentModel;
    public $foreignKey;
    public $newSectionTitleId;
    public $newSectionText;
    public string $modelClass;

    public function mount($model, $parentModel = null, $foreignKey = 'jurisdiction_id')
    {
        $this->model = $model;
        $this->parentModel = $parentModel;
        $this->foreignKey = $foreignKey;
        $this->modelClass = get_class($model);

        if ($this->model->exists) {
            $this->newSectionTitleId = $this->model->section_title_id;
            $this->newSectionText = $this->model->text;
        }
    }

    public function save($addNew = false)
    {
        $this->model = $this->performSave(
            $this->model,
            ['section_title_id' => $this->newSectionTitleId, 'text' => $this->newSectionText],
            $this->parentModel,
            $this->foreignKey,
            $addNew
        );

        if ($addNew) $this->resetForm();
    }

    public function resetForm()
    {
        $this->model = new $this->modelClass;
        $this->newSectionTitleId = null;
        $this->newSectionText = '';
    }

    public function closeEditor()
    {
        // Tell the parent component to hide this form
        $this->dispatch('cancelAddSection');
    }

    public function render()
    {
        $usedTitleIds = $this->modelClass::query()
            ->when($this->model->exists, fn($q) => $q->where('id', '!=', $this->model->id))
            ->when($this->parentModel, fn($q) => $q->where($this->foreignKey, $this->parentModel->id))
            ->pluck('section_title_id');

        return view('livewire.section.edit-section', [
            'availableSectionTitles' => SectionTitle::whereNotIn('id', $usedTitleIds)->orderBy('sort_order')->get()
        ]);
    }
}

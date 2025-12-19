<?php

namespace App\Livewire\Section;

use Livewire\Component;
use App\Models\Section;
use App\Models\DefaultSection; // Add this
use App\Models\SectionTitle;

class EditSection extends Component
{
    public $model;
    public $parentModel;
    public $foreignKey;
    public $newSectionTitleId;
    public $newSectionText;

    // 1. Add a property to track the model class
    public string $modelClass;

    public function mount($model, $parentModel = null, $foreignKey = 'jurisdiction_id')
    {
        $this->model = $model;
        $this->parentModel = $parentModel;
        $this->foreignKey = $foreignKey;

        // 2. Store the class name of the model passed in
        $this->modelClass = get_class($model);

        if ($this->model->exists) {
            $this->newSectionTitleId = $this->model->section_title_id;
            $this->newSectionText = $this->model->text;
        }
    }

    public function save($addNew = false)
    {
        $this->validate([
            'newSectionTitleId' => 'required',
            'newSectionText' => 'required|string',
        ]);

        $isNew = !$this->model->exists;

        $data = [
            'section_title_id' => $this->newSectionTitleId,
            'text' => $this->newSectionText,
        ];

        // 3. Only apply foreign key if we have a parent (avoids DefaultSection errors)
        if ($isNew && $this->parentModel) {
            $data[$this->foreignKey] = $this->parentModel->id;
        }

        $this->model->fill($data)->save();

        $this->dispatch('notify',
            message: $isNew ? "Created successfully." : "Updated successfully.",
            type: 'success'
        );

        $this->dispatch($isNew ? 'sectionAdded' : 'sectionUpdated', keepAdding: $addNew);

        if ($addNew) {
            $this->resetForm();
        }
    }

    public function resetForm()
    {
        // 4. Use the stored class name to instantiate the correct model type
        $this->model = new $this->modelClass;

        $this->newSectionTitleId = null;
        $this->newSectionText = '';
    }

    public function render()
    {
        // Logic for available titles
        $query = $this->modelClass::query();

        if ($this->parentModel) {
            $usedTitleIds = $this->parentModel->sections()
                ->when($this->model->exists, fn($q) => $q->where('id', '!=', $this->model->id))
                ->pluck('section_title_id');
        } else {
            $usedTitleIds = $query
                ->when($this->model->exists, fn($q) => $q->where('id', '!=', $this->model->id))
                ->pluck('section_title_id');
        }

        return view('livewire.section.edit-section', [
            'availableSectionTitles' => SectionTitle::whereNotIn('id', $usedTitleIds)->get()
        ]);
    }
}

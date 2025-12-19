<?php

namespace App\Livewire\Concerns;

use App\Actions\SaveSectionAction;
use Illuminate\Database\Eloquent\Model;

trait HandlesSectionSaving
{
    public function performSave(Model $model, array $data, ?Model $parent = null, string $foreignKey = 'jurisdiction_id', bool $addNew = false)
    {
        $isNew = !$model->exists;

        $savedModel = app(SaveSectionAction::class)->execute($model, $data, $parent, $foreignKey);

        $this->dispatch('notify',
            message: $isNew ? "Created successfully." : "Updated successfully.",
            type: 'success'
        );

        $this->dispatch($isNew ? 'sectionAdded' : 'sectionUpdated', keepAdding: $addNew);

        return $savedModel;
    }
}

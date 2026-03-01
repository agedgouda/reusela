<?php

namespace App\Livewire\Concerns;

use App\Actions\SaveSectionAction;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Validation\ValidationException;

trait HandlesSectionSaving
{
    public function performSave(Model $model, array $data, ?Model $parent = null, string $foreignKey = 'jurisdiction_id', bool $addNew = false)
    {
        $isNew = !$model->exists;

        try {
            $savedModel = app(SaveSectionAction::class)->execute($model, $data, $parent, $foreignKey);

            $this->dispatch('notify',
                message: $isNew ? "Created successfully." : "Updated successfully.",
                type: 'success'
            );

            $this->dispatch($isNew ? 'sectionAdded' : 'sectionUpdated', keepAdding: $addNew);

            return $savedModel;

        } catch (ValidationException $e) {
            // Re-map Action keys ('text') to Blade keys ('newSectionText')
            throw ValidationException::withMessages([
                'newSectionTitleId' => $e->validator->errors()->first('section_title_id'),
                'newSectionText'    => $e->validator->errors()->first('text'),
            ]);
        } catch (\Exception $e) {
            // Handle database crashes, integrity wipes, or 500 errors here
            report($e);

            $this->dispatch('notify',
                message: "A system error occurred while saving.",
                type: 'danger'
            );

            return null;
        }
    }
}

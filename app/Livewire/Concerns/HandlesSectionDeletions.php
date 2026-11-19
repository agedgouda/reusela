<?php

namespace App\Livewire\Concerns;

use App\Actions\DeleteSectionAction;
use App\Models\Section;

trait HandlesSectionDeletions
{
    /**
     * @param  class-string<Section>  $modelClass
     */
    protected function performDelete(int|string $id, string $modelClass, string $notifyPrefix = 'Section'): void
    {
        $this->authorizeEditing();

        $section = $modelClass::find($id);

        if (! $section) {
            return;
        }

        $name = app(DeleteSectionAction::class)->execute($section);

        $this->refreshSections();

        $this->dispatch('notify',
            message: "{$notifyPrefix} '{$name}' deleted successfully.",
            type: 'success'
        );
    }
}

<?php

namespace App\Livewire\Concerns;

use App\Actions\DeleteSectionAction;

trait HandlesSectionDeletions
{
    public function performDelete($id, string $modelClass, string $notifyPrefix = 'Section')
    {
        $section = $modelClass::find($id);

        if ($section) {
            // CALLING THE ACTION HERE
            $name = app(DeleteSectionAction::class)->execute($section);

            if (method_exists($this, 'refreshSections')) {
                $this->refreshSections();
            }

            $this->dispatch('notify',
                message: "{$notifyPrefix} '{$name}' deleted successfully.",
                type: 'success'
            );
        }
    }
}

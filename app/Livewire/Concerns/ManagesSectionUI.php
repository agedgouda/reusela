<?php

namespace App\Livewire\Concerns;

trait ManagesSectionUI
{
    public function toggleEdit($target): void
    {
        if ($target === 'general') {
            $this->showGeneralInfoEdit = !($this->showGeneralInfoEdit ?? false);
            $this->editingSectionId = null;
        } else {
            $this->editingSectionId = ($this->editingSectionId == $target) ? null : $target;
            $this->showGeneralInfoEdit = false;
        }
    }

    public function closeEditor()
    {
        $this->showAddSectionForm = false;
        $this->editingSectionId = null;
        $this->showGeneralInfoEdit = false;
    }

    // Alias to support the hideAddSectionForm name if used elsewhere
    public function hideAddSectionForm()
    {
        $this->closeEditor();
    }
}

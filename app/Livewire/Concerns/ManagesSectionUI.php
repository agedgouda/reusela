<?php

namespace App\Livewire\Concerns;

trait ManagesSectionUI
{
    /**
     * Open or close the editor for the general information ('general') or a section id.
     * Opening an editor renders child editing components, so it requires a login.
     *
     * @param  int|string|array{sectionId?: int|string}  $target
     */
    public function toggleEdit(int|string|array $target): void
    {
        $this->authorizeEditing();

        if (is_array($target)) {
            $target = $target['sectionId'] ?? null;
        }

        if ($target === 'general') {
            $this->showGeneralInfoEdit = ! $this->showGeneralInfoEdit;
            $this->editingSectionId = null;
        } else {
            $this->editingSectionId = ($this->editingSectionId == $target) ? null : $target;
            $this->showGeneralInfoEdit = false;
        }
    }

    public function closeEditor(): void
    {
        $this->showAddSectionForm = false;
        $this->editingSectionId = null;
        $this->showGeneralInfoEdit = false;
    }

    /**
     * Alias to support the hideAddSectionForm name if used elsewhere.
     */
    public function hideAddSectionForm(): void
    {
        $this->closeEditor();
    }
}

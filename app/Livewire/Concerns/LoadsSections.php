<?php

namespace App\Livewire\Concerns;

use App\Actions\GetSectionsAction;
use App\Models\SectionTitle;
use Illuminate\Support\Collection;

trait LoadsSections
{
    public Collection $sections;
    public bool $showAddSectionButton = true;

    /**
     * The core data loading method
     */
public function loadSections(string $modelClass, mixed $parentId = null, string $foreignKey = 'jurisdiction_id')
{
    $actionResults = app(GetSectionsAction::class)->execute($modelClass, $parentId, $foreignKey);

    if ($actionResults->isEmpty()) {
        $this->sections = $this->jurisdiction->display_sections;
    } else {
        $this->sections = $actionResults;
    }
}
    /**
     * Shared refresh logic for both Show and DefaultContent
     */
    public function traitRefreshSections(string $modelClass, mixed $parentId = null, string $foreignKey = 'jurisdiction_id', bool $keepAdding = false)
    {
        $this->editingSectionId = null;
        $this->showAddSectionForm = $keepAdding;

        $this->loadSections($modelClass, $parentId, $foreignKey);
        $this->updateButtonVisibility();
    }

    /**
     * Shared visibility logic
     */
    public function updateButtonVisibility()
    {
        $this->showAddSectionButton = SectionTitle::count() !== $this->sections->count();
    }
}

<?php

namespace App\Livewire\Concerns;

use App\Actions\GetSectionsAction;
use App\Models\Section;
use App\Models\SectionTitle;
use Illuminate\Support\Collection;

trait LoadsSections
{
    /**
     * @var Collection<int, Section>
     */
    public Collection $sections;

    public bool $showAddSectionButton = true;

    /**
     * The core data loading method.
     *
     * @param  class-string<Section>  $modelClass
     */
    protected function loadSections(string $modelClass, mixed $parentId = null, string $foreignKey = 'jurisdiction_id'): void
    {
        $actionResults = app(GetSectionsAction::class)->execute($modelClass, $parentId, $foreignKey);

        $this->sections = $actionResults->isEmpty()
            ? $this->jurisdiction->display_sections
            : $actionResults;
    }

    /**
     * Shared refresh logic for both Show and DefaultContent.
     *
     * @param  class-string<Section>  $modelClass
     */
    protected function traitRefreshSections(string $modelClass, mixed $parentId = null, string $foreignKey = 'jurisdiction_id', bool $keepAdding = false): void
    {
        $this->editingSectionId = null;
        $this->showAddSectionForm = $keepAdding;

        $this->loadSections($modelClass, $parentId, $foreignKey);
        $this->updateButtonVisibility();
    }

    /**
     * Shared visibility logic.
     */
    protected function updateButtonVisibility(): void
    {
        $this->showAddSectionButton = SectionTitle::count() !== $this->sections->count();
    }
}

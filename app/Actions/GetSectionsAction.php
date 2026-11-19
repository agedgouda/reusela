<?php

namespace App\Actions;

use App\Models\Section;
use Illuminate\Database\Eloquent\Collection;

class GetSectionsAction
{
    /**
     * Fetches sections for either a specific Jurisdiction or Global Defaults.
     *
     * @param  class-string<Section>  $modelClass
     * @return Collection<int, Section>
     */
    public function execute(string $modelClass, mixed $parentId = null, string $foreignKey = 'jurisdiction_id'): Collection
    {
        return $modelClass::with('sectionTitle')
            ->when($parentId, fn ($query) => $query->where($foreignKey, $parentId))
            ->get()
            ->sortBy(fn (Section $section) => $section->sectionTitle->sort_order ?? 0);
    }
}

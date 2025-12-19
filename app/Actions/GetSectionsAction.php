<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Collection;

class GetSectionsAction
{
    /**
     * Fetches sections for either a specific Jurisdiction or Global Defaults.
     */
    // Change ?int to mixed
    public function execute(string $modelClass, mixed $parentId = null, string $foreignKey = 'jurisdiction_id'): Collection
    {
        return $modelClass::with('sectionTitle')
            ->when($parentId, fn($q) => $q->where($foreignKey, $parentId))
            ->get()
            ->sortBy(fn($item) => $item->sectionTitle->sort_order);
    }
}

<?php

namespace App\Livewire\Jurisdiction;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Jurisdiction;
use App\Services\SortingService;
use Illuminate\Pipeline\Pipeline;

class Index extends Component
{
    use WithPagination;

    public string $sortField = 'name';  // Default sort field
    public string $sortDirection = 'asc';  // Default sort direction
    public string $filter = '';  // Search filter

    protected $queryString = [
        'filter' => ['except' => ''],  // Keep filter between refreshes
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    /**
     * Toggle or set sorting for a specific field.
     */
    public function sortBy(string $field): void
    {
        list($this->sortField, $this->sortDirection) = SortingService::handleSort(
            $this->sortField, $this->sortDirection, $field
        );
    }

    /**
     * Query the filtered and sorted jurisdictions.
     */
    private function getFilteredJurisdictions()
    {
        return app(Pipeline::class)
            ->send(Jurisdiction::query())
            ->through([
                new \App\Filters\NameFilter($this->filter),
            ])
            ->thenReturn()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(25);
    }

    public function render()
    {
        $jurisdictions = $this->getFilteredJurisdictions();

        return view('livewire.jurisdiction.index', [
            'jurisdictions' => $jurisdictions,
        ]);
    }
}

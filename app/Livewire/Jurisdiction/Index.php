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
    public string $tab = 'list';

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
            ->send(Jurisdiction::query()->withCount('sections'))
            ->through([
                new \App\Filters\NameFilter($this->filter),
            ])
            ->thenReturn()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(25);
    }

    public function switchTab(string $tab)
    {
        $this->tab = $tab;
    }

    #[On('tabSwitched')]
    public function handleTabSwitched(string $tab): void
    {
        $this->tab = $tab;
    }

    public function getDefaultJurisdictionProperty()
    {
        // Use the method we added to the Model to bypass the Global Scope
        return \App\Models\Jurisdiction::withoutGlobalScope('excludeDefault')
            ->where('is_system_default', true)
            ->first();
    }

    public function render()
    {
       // 1. Check for the flash data set by the 'Cancel' button on the previous request.
        if (session()->has('active_tab')) {
            $this->tab = session('active_tab');
        }

        elseif (request()->query('tab')) {
            $this->tab = request()->query('tab');
        }

        $jurisdictions = $this->getFilteredJurisdictions();

        return view('livewire.jurisdiction.index', [
            'jurisdictions' => $jurisdictions,
        ]);
    }
}

<?php

namespace App\Livewire\Jurisdiction;

use App\Models\County;
use App\Models\Jurisdiction;
use App\Services\SortingService;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $sortField = 'name';  // Default sort field

    public string $sortDirection = 'asc';  // Default sort direction

    public string $filter = '';  // Search filter

    public string $tab = 'list';

    public string $newJurisdictionName = '';

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
        [$this->sortField, $this->sortDirection] = SortingService::handleSort(
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

    public function cancelCreate(): void
    {
        $this->newJurisdictionName = '';
        $this->resetErrorBag('newJurisdictionName');
    }

    public function createJurisdiction(): mixed
    {
        $this->newJurisdictionName = Str::title($this->newJurisdictionName);

        $this->validate([
            'newJurisdictionName' => ['required', 'string', 'max:255', Rule::unique('jurisdictions', 'name')],
        ]);

        $laCounty = County::where('name', 'Los Angeles County')->firstOrFail();

        $jurisdiction = Jurisdiction::create([
            'county_id' => $laCounty->id,
            'name' => $this->newJurisdictionName,
        ]);

        return redirect()->route('jurisdiction.show', $jurisdiction->id);
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
        } elseif (request()->query('tab')) {
            $this->tab = request()->query('tab');
        }

        $jurisdictions = $this->getFilteredJurisdictions();

        return view('livewire.jurisdiction.index', [
            'jurisdictions' => $jurisdictions,
        ]);
    }
}

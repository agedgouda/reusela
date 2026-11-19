<?php

namespace App\Livewire\Jurisdiction;

use App\Filters\NameFilter;
use App\Models\County;
use App\Models\Jurisdiction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
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

    /**
     * Columns the list may be sorted by (the values come from the query string).
     *
     * @var list<string>
     */
    private const SORTABLE_FIELDS = ['name', 'sections_count', 'created_at', 'updated_at'];

    /**
     * @var list<string>
     */
    private const TABS = ['list', 'content', 'default'];

    /**
     * @var array<string, array<string, string>>
     */
    protected $queryString = [
        'filter' => ['except' => ''],  // Keep filter between refreshes
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    /**
     * Query the filtered and sorted jurisdictions.
     */
    /**
     * @return LengthAwarePaginator<int, Jurisdiction>
     */
    private function getFilteredJurisdictions(): LengthAwarePaginator
    {
        /** @var Builder<Jurisdiction> $query */
        $query = app(Pipeline::class)
            ->send(Jurisdiction::query()->withCount('sections'))
            ->through([
                new NameFilter($this->filter),
            ])
            ->thenReturn();

        $sortField = in_array($this->sortField, self::SORTABLE_FIELDS, true) ? $this->sortField : 'name';
        $sortDirection = $this->sortDirection === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sortField, $sortDirection)->paginate(25);
    }

    public function cancelCreate(): void
    {
        $this->newJurisdictionName = '';
        $this->resetErrorBag('newJurisdictionName');
    }

    public function createJurisdiction(): void
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

        $this->redirectRoute('jurisdiction.show', $jurisdiction->id);
    }

    public function switchTab(string $tab): void
    {
        $this->setTab($tab);
    }

    #[On('tabSwitched')]
    public function handleTabSwitched(string $tab): void
    {
        $this->setTab($tab);
    }

    private function setTab(mixed $tab): void
    {
        if (is_string($tab) && in_array($tab, self::TABS, true)) {
            $this->tab = $tab;
        }
    }

    public function getDefaultJurisdictionProperty(): ?Jurisdiction
    {
        // Use the method we added to the Model to bypass the Global Scope
        return Jurisdiction::withoutGlobalScope('excludeDefault')
            ->where('is_system_default', true)
            ->first();
    }

    public function render(): View
    {
        // 1. Check for the flash data set by the 'Cancel' button on the previous request.
        if (session()->has('active_tab')) {
            $this->setTab(session('active_tab'));
        } elseif (request()->query('tab')) {
            $this->setTab(request()->query('tab'));
        }

        $jurisdictions = $this->getFilteredJurisdictions();

        return view('livewire.jurisdiction.index', [
            'jurisdictions' => $jurisdictions,
        ]);
    }
}

<?php

namespace App\Livewire\SectionTitle;

use App\Models\SectionTitle;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    /**
     * Array of arrays to avoid model serialization problems in nested/Flux contexts.
     *
     * @var list<array{id: string, title: string, sort_order: int|null, icon: string|null}>
     */
    public array $titles = [];

    public function mount(): void
    {
        $this->loadTitles();
    }

    protected function loadTitles(): void
    {
        $titles = SectionTitle::orderBy('sort_order')->get()
            ->map(fn (SectionTitle $title) => [
                'id' => (string) $title->id,
                'title' => $title->title,
                'sort_order' => $title->sort_order,
                'icon' => $title->icon,
            ])
            ->all();

        $this->titles = array_values($titles);
    }

    /**
     * Called from the browser when Sortable finishes.
     *
     * @param  array<int, int|string>  $orderedIds
     */
    public function updateSortOrder(array $orderedIds): void
    {
        foreach (array_values($orderedIds) as $index => $id) {
            SectionTitle::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        // reload titles so blade reflects the new order
        $this->loadTitles();
    }

    public function render(): View
    {
        return view('livewire.section-title.index');
    }
}

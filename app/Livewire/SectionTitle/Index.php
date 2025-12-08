<?php

namespace App\Livewire\SectionTitle;

use Livewire\Component;
use App\Models\SectionTitle;

class Index extends Component
{
    // Use array of arrays to avoid model serialization problems in nested/Flux contexts
    public array $titles = [];

    public function mount()
    {
        $this->loadTitles();
    }

    protected function loadTitles(): void
    {
        $this->titles = SectionTitle::orderBy('sort_order')->get()->map(function ($t) {
            return [
                'id' => (string) $t->id,
                'title' => $t->title,
                'sort_order' => $t->sort_order,
            ];
        })->toArray();
    }

    /**
     * Called from the browser when Sortable finishes.
     * $orderedIds will be an array of ids (strings).
     */
    public function updateSortOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            SectionTitle::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        // reload titles so blade reflects the new order
        $this->loadTitles();
    }

    public function render()
    {
        return view('livewire.section-title.index');
    }
}

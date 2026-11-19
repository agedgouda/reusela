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
     * Called by wire:sort when a title is dropped at a new zero-based position.
     */
    public function moveTitle(int|string $id, int $position): void
    {
        /** @var list<string> $orderedIds */
        $orderedIds = [];

        foreach (SectionTitle::query()->orderBy('sort_order')->orderBy('id')->pluck('id') as $titleId) {
            if (is_int($titleId) || is_string($titleId)) {
                $orderedIds[] = (string) $titleId;
            }
        }

        $currentIndex = array_search((string) $id, $orderedIds, true);

        if ($currentIndex === false) {
            return;
        }

        array_splice($orderedIds, $currentIndex, 1);
        array_splice($orderedIds, max(0, min($position, count($orderedIds))), 0, [(string) $id]);

        foreach ($orderedIds as $index => $titleId) {
            SectionTitle::whereKey($titleId)->update(['sort_order' => $index + 1]);
        }

        $this->loadTitles();
    }

    public function render(): View
    {
        return view('livewire.section-title.index');
    }
}

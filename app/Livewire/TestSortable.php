<?php

namespace App\Livewire;

use Livewire\Component;

class TestSortable extends Component
{
public array $fluxTestItems = [
    ['id' => 1, 'label' => 'Alpha'],
    ['id' => 2, 'label' => 'Bravo'],
    ['id' => 3, 'label' => 'Charlie'],
];

public function fluxTestReorder(array $orderedIds)
{
    \Log::info("FLUX TEST: reorder called", ['ids' => $orderedIds]);

    $newOrder = [];
    foreach ($orderedIds as $id) {
        foreach ($this->fluxTestItems as $item) {
            if ($item['id'] == $id) $newOrder[] = $item;
        }
    }

    $this->fluxTestItems = $newOrder;

    \Log::info("FLUX TEST: new order saved", ['items' => $this->fluxTestItems]);
}


    public function render()
    {
        return view('livewire.test-sortable');
    }
}

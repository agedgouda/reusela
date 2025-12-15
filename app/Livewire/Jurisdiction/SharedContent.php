<?php

namespace App\Livewire\Jurisdiction;

use Livewire\Component;

class SharedContent extends Component
{
    public function render()
    {
        return view('livewire.jurisdiction.shared-content', [
            'content' => cache()->rememberForever(
                'shared.jurisdiction.show',
                fn () => \App\Models\SharedContent::where('key', 'jurisdiction.show')->value('content')
            )
        ]);
    }
}

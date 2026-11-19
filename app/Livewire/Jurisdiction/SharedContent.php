<?php

namespace App\Livewire\Jurisdiction;

use App\Models\SharedContent as SharedContentModel;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SharedContent extends Component
{
    public function render(): View
    {
        return view('livewire.jurisdiction.shared-content', [
            'content' => cache()->rememberForever(
                'shared.jurisdiction.show',
                fn () => SharedContentModel::where('key', 'jurisdiction.show')->value('content')
            ),
        ]);
    }
}

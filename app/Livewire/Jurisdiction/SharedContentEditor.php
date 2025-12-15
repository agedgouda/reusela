<?php

namespace App\Livewire\Jurisdiction;

use Livewire\Component;
use App\Models\SharedContent;

class SharedContentEditor extends Component
{
    public string $content = '';

    public $originalContent;

    public function mount()
    {
        // Load the initial content only
        $this->content = SharedContent::where('key', 'jurisdiction.show')->value('content') ?? '';
        $this->originalContent = $this->content;
    }

    public function save()
    {
        SharedContent::updateOrCreate(['key' => 'jurisdiction.show'], ['content' => $this->content]);
        $this->dispatch('content-saved', content: $this->content);

        session()->flash('saved', true);
    }
    public function cancel()
    {
        session()->flash('active_tab', 'content');
        $currentUrl = request()->header('referer') ?: url()->current();
        return redirect($currentUrl)->with('active_tab', 'content');
    }
    public function render()
    {
        return view('livewire.jurisdiction.shared-content-editor');
    }
}

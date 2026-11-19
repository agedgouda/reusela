<?php

namespace App\Livewire\Jurisdiction;

use App\Livewire\Concerns\InteractsWithAuthenticatedUser;
use App\Models\SharedContent;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SharedContentEditor extends Component
{
    use InteractsWithAuthenticatedUser;

    public string $content = '';

    public string $originalContent = '';

    public function mount(): void
    {
        $this->authorizeEditing();

        // Load the initial content only
        $content = SharedContent::where('key', 'jurisdiction.show')->value('content');
        $this->content = is_string($content) ? $content : '';
        $this->originalContent = $this->content;
    }

    public function save(): void
    {
        $this->authorizeEditing();

        SharedContent::updateOrCreate(
            ['key' => 'jurisdiction.show'],
            ['content' => $this->content]
        );

        cache()->forget('shared.jurisdiction.show');

        $this->dispatch('content-saved', content: $this->content);
        session()->flash('saved', true);
    }

    public function cancel(): void
    {
        session()->flash('active_tab', 'content');

        $this->redirectRoute('jurisdiction.index');
    }

    public function render(): View
    {
        return view('livewire.jurisdiction.shared-content-editor');
    }
}

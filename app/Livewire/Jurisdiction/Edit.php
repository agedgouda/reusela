<?php

namespace App\Livewire\Jurisdiction;

use App\Livewire\Concerns\InteractsWithAuthenticatedUser;
use App\Models\Jurisdiction;
use App\Support\HtmlLinks;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Edit extends Component
{
    use InteractsWithAuthenticatedUser;

    #[Locked]
    public Jurisdiction $jurisdiction;

    public string $text = '';

    public function mount(Jurisdiction $jurisdiction): void
    {
        $this->authorizeEditing();

        $this->jurisdiction = $jurisdiction;
        $this->text = $jurisdiction->general_information ?? '';
    }

    public function save(): void
    {
        $this->authorizeEditing();

        $this->jurisdiction->update([
            'general_information' => HtmlLinks::ensureNewTab($this->text),
        ]);

        $this->dispatch('toggleEdit', 'general');
    }

    public function cancel(): void
    {
        $this->dispatch('toggleEdit', 'general');
    }

    public function render(): View
    {
        return view('livewire.jurisdiction.edit');
    }
}

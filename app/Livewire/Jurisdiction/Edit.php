<?php

namespace App\Livewire\Jurisdiction;

use App\Models\Jurisdiction;
use App\Support\HtmlLinks;
use Livewire\Component;

class Edit extends Component
{
    public Jurisdiction $jurisdiction;

    public string $text = '';

    public function mount(Jurisdiction $jurisdiction)
    {
        $this->jurisdiction = $jurisdiction;
        $this->text = $jurisdiction->general_information ?? '';
    }

    public function save()
    {

        $this->jurisdiction->update([
            'general_information' => HtmlLinks::ensureNewTab($this->text),
        ]);
        $this->dispatch('toggleEdit', 'general');
    }

    public function cancel()
    {
        $this->dispatch('toggleEdit', 'general');
    }

    public function render()
    {
        return view('livewire.jurisdiction.edit');
    }
}

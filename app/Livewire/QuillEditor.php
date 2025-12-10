<?php

namespace App\Livewire;

use Livewire\Component;

class QuillEditor extends Component
{
    public string $content = '';
    public string $model;
    public string $id;

    public function mount(string $model, string $value = '')
    {
        $this->model = $model;       // Parent property name
        $this->content = $value;     // Initial HTML
        $this->id = 'quill-' . uniqid();
    }

    public function updatedContent(): void
    {
        $this->dispatchInput($this->model, $this->content);
    }

    public function render()
    {
        return view('livewire.quill-editor');
    }
}

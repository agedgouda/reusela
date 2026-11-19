<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Show extends Component
{
    public User $user;

    public bool $confirmingDelete = false;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function confirmDelete(): void
    {
        $this->confirmingDelete = true;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDelete = false;
    }

    public function deleteUser(): void
    {
        $this->user->delete();

        $this->redirectRoute('user.index');
    }

    public function render(): View
    {
        return view('livewire.user.show');
    }
}

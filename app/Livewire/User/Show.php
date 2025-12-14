<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;

class Show extends Component
{
    public User $user;

    public bool $confirmingDelete = false;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function confirmDelete()
    {
        $this->confirmingDelete = true;
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = false;
    }

    public function deleteUser()
    {
        $this->user->delete();

        return redirect('/user');
    }

    public function render()
    {
        return view('livewire.user.show');
    }
}

<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;

class Index extends Component
{
    public $users; // Optional: if you want to make it accessible in the template directly

    public function mount()
    {
        // Fetch all users when the component is mounted
        $this->users = User::all();
    }

    public function render()
    {
        return view('livewire.user.index', [
            'users' => $this->users, // pass users to the view
        ]);
    }
}

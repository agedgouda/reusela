<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Create extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ];

    // Accept a parameter to determine the action after saving
    public function createUser($action = 'redirect')
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        if ($action === 'redirect') {
            return redirect('/user');
        }

        // Otherwise reset form for "save & add new"
        $this->reset(['name', 'email', 'password', 'password_confirmation']);
        session()->flash('status', 'User created successfully. You can add another one.');
    }

    public function render()
    {
        return view('livewire.user.create');
    }
}

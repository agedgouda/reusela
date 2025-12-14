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

    // Save and redirect to /user
    public function saveAndRedirect()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

       //$this->redirect('/user');
        return redirect()->route('user.index');
    }

    // Save and reset form for adding another user
    public function saveAndAddNew()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['name', 'email', 'password', 'password_confirmation']);
        session()->flash('status', 'User created successfully. You can add another one.');
        return redirect()->route('user.create');
    }

    public function render()
    {
        return view('livewire.user.create');
    }
}

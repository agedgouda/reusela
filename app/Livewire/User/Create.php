<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Create extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * @var array<string, string>
     */
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ];

    /**
     * Save and redirect to the user list.
     */
    public function saveAndRedirect(): void
    {
        $this->createUser();

        $this->redirectRoute('user.index');
    }

    /**
     * Save and reset the form for adding another user.
     */
    public function saveAndAddNew(): void
    {
        $this->createUser();

        $this->reset(['name', 'email', 'password', 'password_confirmation']);
        session()->flash('status', 'User created successfully.');

        $this->redirectRoute('user.create');
    }

    private function createUser(): void
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);
    }

    public function render(): View
    {
        return view('livewire.user.create');
    }
}

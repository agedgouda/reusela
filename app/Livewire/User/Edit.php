<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Illuminate\Validation\Rule;

class Edit extends Component
{
    public User $user;

    public string $name;
    public string $email;

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
        ];
    }

    public function mount(User $user)
    {
        $this->user  = $user;
        $this->name  = $user->name;
        $this->email = $user->email;
    }

    public function save()
    {
        $this->validate();

        $this->user->update([
            'name'  => $this->name,
            'email' => $this->email,
        ]);

        return redirect("/user/{$this->user->id}");
    }

    public function render()
    {
        return view('livewire.user.edit');
    }
}

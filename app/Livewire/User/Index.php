<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Index extends Component
{
    /**
     * @var Collection<int, User>
     */
    public Collection $users;

    public function mount(): void
    {
        $this->users = User::all();
    }

    public function render(): View
    {
        return view('livewire.user.index', [
            'users' => $this->users,
        ]);
    }
}

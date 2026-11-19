<?php

namespace App\Livewire\Settings;

use App\Livewire\Actions\Logout;
use App\Livewire\Concerns\InteractsWithAuthenticatedUser;
use Livewire\Component;

class DeleteUserForm extends Component
{
    use InteractsWithAuthenticatedUser;

    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap($this->authenticatedUser(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}

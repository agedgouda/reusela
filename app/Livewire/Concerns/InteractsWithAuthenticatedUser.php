<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

trait InteractsWithAuthenticatedUser
{
    /**
     * The logged-in user. Aborts with 403 when nobody is logged in.
     */
    protected function authenticatedUser(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, Response::HTTP_FORBIDDEN);

        return $user;
    }

    /**
     * Every logged-in user is an admin, so editing only requires a login. Components
     * that are also embedded on the public search page must call this in every action
     * that changes data, because Livewire actions can be called directly by any visitor.
     */
    protected function authorizeEditing(): void
    {
        $this->authenticatedUser();
    }
}

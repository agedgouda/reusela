<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

trait InteractsWithAuthenticatedUser
{
    /**
     * The logged-in user. These components only run behind the auth middleware,
     * so a missing user means the session ended mid-request.
     */
    protected function authenticatedUser(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, Response::HTTP_FORBIDDEN);

        return $user;
    }
}

<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function manageAll(User $user): bool
    {
        return in_array($user->role, ['admin']);
    }

    public function manageAdmin(User $user): bool
    {
        // admin & user selalu boleh
        if (in_array($user->role, ['admin', 'user'])) {
            return true;
        }        

        return false;
    }

    public function manageKorlap(User $user): bool
    {
        // admin & user & korlap selalu boleh
        if (in_array($user->role, ['admin', 'user', 'korlap'])) {
            return true;
        }        

        return false;
    }
}

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
        return in_array($user->role, ['superadmin']);
    }

    public function manageAdmin(User $user): bool
    {
        // superadmin & admin selalu boleh
        if (in_array($user->role, ['superadmin', 'admin'])) {
            return true;
        }        

        return false;
    }

    public function manageKorlap(User $user): bool
    {
        // superadmin & admin selalu boleh
        if (in_array($user->role, ['superadmin', 'admin', 'korlap'])) {
            return true;
        }        

        return false;
    }
}

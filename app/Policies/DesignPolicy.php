<?php

namespace App\Policies;

use App\Models\Design;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DesignPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Design $design): bool
    {
        return $user->id === $design->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Design $design): bool
    {
        return $user->id === $design->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Design $design): bool
    {
        return $user->id === $design->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Design $design): bool
    {
        return $user->id === $design->user_id;
    }
}

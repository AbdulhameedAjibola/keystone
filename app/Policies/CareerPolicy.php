<?php

namespace App\Policies;

use App\Models\Career;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CareerPolicy
{
    //     public function before(User $user, $capability)
    // {
    //     if ($user->role === 'admin') {
    //         return true; // Admins are authorized for EVERYTHING in this policy
    //     }
    // }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Career $career): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
       return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Career $career): bool
    {
        return $user->id === $career->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Career $career): bool
    {
        return $user->id === $career->user_id;
    }

    /**
     * Determine whether the user can restore the career.
     */
    public function restore(User $user, Career $career): bool
    {
        return $user->id === $career->user_id;
    }

    /**
     * Determine whether the user can permanently delete the career
     */
    public function forceDelete(User $user, Career $career): bool
    {
        return $user->id === $career->user_id;
    }
}

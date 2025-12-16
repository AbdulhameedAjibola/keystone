<?php

namespace App\Policies;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InquiryPolicy
{
    
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Inquiry $inquiry): bool
    {
        return $inquiry->user_id === $user->id;
    }

   

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Inquiry $inquiry): bool
    {
        return $inquiry->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Inquiry $inquiry): bool
    {
        return $inquiry->user_id === $user->id;
    }

    public function read(User $user, Inquiry $inquiry)
    {
        if($user->role === 'admin'){
            return true;
        }
    }

    //  //admin can do anything
    public function before (User $user, $ability){
        if($user->role === 'admin'){
            return true;
        }
    }


}

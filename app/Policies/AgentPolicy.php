<?php

namespace App\Policies;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AgentPolicy
{
   //agent can only start verification for themselves
    public function startVerification(Agent $agent, Agent $target){
        return $agent->id === $target->id;
    }

    //only admin can verify agents
    public function verify(Agent $agent, User $user){
        if($user->role === 'admin'){
            return true;
        }
    }

    //admin can do anything
    public function before (User $user, $ability){
        if($user->role === 'admin'){
            return true;
        }
    }
}

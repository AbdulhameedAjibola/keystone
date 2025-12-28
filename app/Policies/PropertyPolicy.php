<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PropertyPolicy
{

    //make sure agent can only update their own properties
     /**
     * Determine whether the agent can update the property.
     */
    public function update(Agent $agent, Property $property): bool
    {
        return $agent->id === $property->agent_id;
    }

    //make sure agent can only delete their own properties
    public function delete(Agent $agent, Property $property): bool
    {
        return $agent->id === $property->agent_id;
    }

    //admin can do anything
    public function before ($user, $ability){
     if ($user instanceof User && $user->role === 'admin') {
        return true;
    }

    return null;
    }
    
}

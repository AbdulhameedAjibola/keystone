<?php

namespace App\Observers;

use App\Models\Media;
use Illuminate\Support\Facades\Log;

class MediaObserver
{
    /**
     * Handle the Media "created" event.
     */
    public function created(Media $media): void
    {
        Log::info("A new media was created with ID: {$media->id}");

        if($media->collection === 'agent_verification'){
            $agent = $media->mediable;
            if($agent && $agent->status === 'unverified'){
               $agent->update(['status' => 'pending']);
                Log::info("Agent ID: {$agent->id} status updated to pending due to new verification media.");
            }
        }
    }

    /**
     * Handle the Media "updated" event.
     */
    public function updated(Media $media): void
    {
        //
    }

    /**
     * Handle the Media "deleted" event.
     */
    public function deleted(Media $media): void
    {
        //
    }

    /**
     * Handle the Media "restored" event.
     */
    public function restored(Media $media): void
    {
        //
    }

    /**
     * Handle the Media "force deleted" event.
     */
    public function forceDeleted(Media $media): void
    {
        //
    }
}

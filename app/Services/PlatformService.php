<?php

namespace App\Services;

use App\Models\User;

class PlatformService
{
    /**
     * Retrieve user avalible platforms.
     */
    public function userAvaliblePlatforms(User $user)
    {
        return $user->activePlatforms;
    }

    public function toggleUserPlatform($platformId ,User $user)
    {

        if ($user->activePlatforms()->where('platform_id', $platformId)->exists()) {
            // Already active -> detach it (disable)
            $user->activePlatforms()->detach($platformId);
            $status = 'disabled';
        } else {
            // Not active -> attach it (enable)
            $user->activePlatforms()->attach($platformId);
            $status = 'enabled';
        }

        return ['data' => true, 'message' => "Platform $status successfully."];
    }


   
    

}

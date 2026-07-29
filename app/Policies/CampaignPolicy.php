<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    /**
     * Administrators control everything.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Campaign $campaign): bool
    {
        return $this->isGameMasterOf($user, $campaign)
            || $campaign->players()->whereKey($user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->isGameMaster();
    }

    public function update(User $user, Campaign $campaign): bool
    {
        return $this->isGameMasterOf($user, $campaign);
    }

    public function delete(User $user, Campaign $campaign): bool
    {
        return $this->isGameMasterOf($user, $campaign);
    }

    private function isGameMasterOf(User $user, Campaign $campaign): bool
    {
        return $user->isGameMaster() && $campaign->game_master_id === $user->id;
    }
}

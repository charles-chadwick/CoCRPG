<?php

namespace App\Actions;

use App\Models\Character;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class GetUserCharactersAction
{
    /**
     * List a user's characters, newest first, for the dashboard.
     *
     * @return Collection<int, Character>
     */
    public function handle(User $user): Collection
    {
        return $user->characters()
            ->with('campaign:id,title')
            ->latest()
            ->get(['id', 'campaign_id', 'name', 'occupation', 'age', 'gender', 'birthplace']);
    }
}

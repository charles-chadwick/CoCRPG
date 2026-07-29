<?php

namespace App\Actions;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Support\Collection;

class GetUserCampaignsAction
{
    /**
     * List the campaigns visible to a user: everything for an administrator,
     * campaigns they run for a Game Master, campaigns they belong to for a Player.
     *
     * Session times are emitted as naive local wall time so that every screen —
     * and the edit form's datetime-local inputs — agree on the same clock.
     *
     * @return Collection<int, array{id: int, title: string, description: ?string, game_master: string, is_game_master: bool, player_count: int, session_count: int, next_session_at: ?string}>
     */
    public function handle(User $user): Collection
    {
        $campaigns = Campaign::query()
            ->with(['gameMaster:id,name', 'nextSession'])
            ->withCount(['players', 'sessions'])
            ->unless($user->isAdmin(), fn ($query) => $query->where(
                fn ($visible) => $visible
                    ->where('game_master_id', $user->id)
                    ->orWhereHas('players', fn ($players) => $players->whereKey($user->id))
            ))
            ->latest()
            ->get();

        return $campaigns->map(fn (Campaign $campaign) => [
            'id' => $campaign->id,
            'title' => $campaign->title,
            'description' => $campaign->description,
            'game_master' => $campaign->gameMaster->name,
            'is_game_master' => $campaign->game_master_id === $user->id,
            'player_count' => $campaign->players_count,
            'session_count' => $campaign->sessions_count,
            'next_session_at' => $campaign->nextSession?->scheduled_at?->format('Y-m-d\TH:i'),
        ])->values();
    }
}

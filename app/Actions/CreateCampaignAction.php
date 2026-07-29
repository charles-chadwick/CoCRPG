<?php

namespace App\Actions;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateCampaignAction
{
    public function __construct(private SyncCampaignSessionsAction $syncSessions) {}

    /**
     * @param  array{title: string, description?: string|null, player_ids?: array<int, int>|null, sessions?: array<int, array<string, mixed>>|null}  $data
     */
    public function handle(User $game_master, array $data): Campaign
    {
        return DB::transaction(function () use ($game_master, $data): Campaign {
            $campaign = Campaign::create([
                'game_master_id' => $game_master->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);

            $campaign->players()->sync($data['player_ids'] ?? []);

            $this->syncSessions->handle($campaign, $data['sessions'] ?? []);

            return $campaign;
        });
    }
}

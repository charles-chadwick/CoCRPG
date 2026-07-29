<?php

namespace App\Actions;

use App\Models\Campaign;
use Illuminate\Support\Facades\DB;

class UpdateCampaignAction
{
    public function __construct(private SyncCampaignSessionsAction $syncSessions) {}

    /**
     * @param  array{title: string, description?: string|null, player_ids?: array<int, int>|null, sessions?: array<int, array<string, mixed>>|null}  $data
     */
    public function handle(Campaign $campaign, array $data): Campaign
    {
        return DB::transaction(function () use ($campaign, $data): Campaign {
            $campaign->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);

            $campaign->players()->sync($data['player_ids'] ?? []);

            $this->syncSessions->handle($campaign, $data['sessions'] ?? []);

            return $campaign->refresh();
        });
    }
}

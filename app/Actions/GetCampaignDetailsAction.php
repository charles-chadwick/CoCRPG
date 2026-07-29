<?php

namespace App\Actions;

use App\Models\Campaign;
use App\Models\CampaignSession;
use App\Models\Character;
use App\Models\User;

class GetCampaignDetailsAction
{
    /**
     * Load a campaign with its relations and shape it for the front end.
     *
     * @return array{
     *     id: int,
     *     title: string,
     *     description: ?string,
     *     game_master: array{id: int, name: string},
     *     players: array<int, array{id: int, name: string, email: string}>,
     *     player_ids: array<int, int>,
     *     sessions: array<int, array{id: int, title: ?string, scheduled_at: string, status: string, notes: ?string}>,
     *     characters: array<int, array{id: int, name: string, occupation: string, player: string}>
     * }
     */
    public function handle(Campaign $campaign): array
    {
        $campaign->load([
            'gameMaster:id,name',
            'players:id,name,email',
            'sessions' => fn ($query) => $query->oldest('scheduled_at'),
            'characters.user:id,name',
        ]);

        return [
            'id' => $campaign->id,
            'title' => $campaign->title,
            'description' => $campaign->description,
            'game_master' => [
                'id' => $campaign->gameMaster->id,
                'name' => $campaign->gameMaster->name,
            ],
            'players' => $campaign->players
                ->map(fn (User $player) => [
                    'id' => $player->id,
                    'name' => $player->name,
                    'email' => $player->email,
                ])->values()->all(),
            'player_ids' => $campaign->players->pluck('id')->all(),
            'sessions' => $campaign->sessions
                ->map(fn (CampaignSession $session) => [
                    'id' => $session->id,
                    'title' => $session->title,
                    'scheduled_at' => $session->scheduled_at->format('Y-m-d\TH:i'),
                    'status' => $session->status->value,
                    'notes' => $session->notes,
                ])->values()->all(),
            'characters' => $campaign->characters
                ->map(fn (Character $character) => [
                    'id' => $character->id,
                    'name' => $character->name,
                    'occupation' => $character->occupation->value,
                    'player' => $character->user->name,
                ])->values()->all(),
        ];
    }
}

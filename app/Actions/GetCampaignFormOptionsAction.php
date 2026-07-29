<?php

namespace App\Actions;

use App\Enums\Campaign\SessionStatus;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Collection;

class GetCampaignFormOptionsAction
{
    /**
     * Build the option lists used by the campaign create and edit screens.
     *
     * @return array{
     *     player_options: Collection<int, array{value: int, label: string}>,
     *     session_status_options: Collection<int, array{value: string, label: string}>
     * }
     */
    public function handle(): array
    {
        return [
            'player_options' => $this->playerOptions(),
            'session_status_options' => $this->sessionStatusOptions(),
        ];
    }

    /**
     * @return Collection<int, array{value: int, label: string}>
     */
    private function playerOptions(): Collection
    {
        return User::query()
            ->role(Role::Player->value)
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(fn (User $player) => [
                'value' => $player->id,
                'label' => "{$player->name} ({$player->email})",
            ])
            ->values();
    }

    /**
     * @return Collection<int, array{value: string, label: string}>
     */
    private function sessionStatusOptions(): Collection
    {
        return collect(SessionStatus::cases())
            ->map(fn (SessionStatus $status) => [
                'value' => $status->value,
                'label' => $status->value,
            ])
            ->values();
    }
}

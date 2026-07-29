<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Campaign;
use App\Models\CampaignSession;
use App\Models\User;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        $game_masters = User::query()->role(Role::GameMaster->value)->get();
        $players = User::query()->role(Role::Player->value)->get();

        if ($game_masters->isEmpty() || $players->isEmpty()) {
            return;
        }

        foreach ($game_masters as $game_master) {
            Campaign::factory()
                ->count(2)
                ->create(['game_master_id' => $game_master->id])
                ->each(function (Campaign $campaign) use ($players): void {
                    $campaign->players()->sync(
                        $players->random(min(3, $players->count()))->pluck('id')
                    );

                    CampaignSession::factory()->count(2)->played()->create([
                        'campaign_id' => $campaign->id,
                    ]);

                    CampaignSession::factory()->count(3)->create([
                        'campaign_id' => $campaign->id,
                    ]);
                });
        }
    }
}

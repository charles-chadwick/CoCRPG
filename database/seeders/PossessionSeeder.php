<?php

namespace Database\Seeders;

use App\Enums\PlayerPossession;
use App\Models\Player;
use App\Models\Possession;
use Illuminate\Database\Seeder;

class PossessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Player::query()->each(function (Player $player): void {
            foreach (PlayerPossession::cases() as $possession) {
                Possession::factory()->create([
                    'player_id' => $player->id,
                    'name' => $possession,
                ]);
            }
        });
    }
}

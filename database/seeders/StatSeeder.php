<?php

namespace Database\Seeders;

use App\Enums\PlayerStat;
use App\Models\Player;
use App\Models\Stat;
use Illuminate\Database\Seeder;

class StatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Player::query()->each(function (Player $player): void {
            foreach (PlayerStat::cases() as $stat) {
                Stat::factory()->create([
                    'player_id' => $player->id,
                    'key' => $stat,
                ]);
            }
        });
    }
}

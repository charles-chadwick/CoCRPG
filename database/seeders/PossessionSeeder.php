<?php

namespace Database\Seeders;

use App\Enums\ModifierSign;
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
            foreach (PlayerPossession::cases() as $type) {
                $possession = Possession::factory()->create(['type' => $type]);

                $pivot = [];

                $modifier = fake()->optional()->numberBetween(-10, 10);
                if ($modifier) {
                    $pivot['modifier'] = $modifier;
                    $pivot['modifier_sign'] = fake()->optional()->randomElement(ModifierSign::cases())?->value;

                }
                $player->possessions()->attach($possession->id, $pivot);
            }
        });
    }
}

<?php

namespace Database\Seeders;

use App\Enums\ModifierSign;
use App\Enums\Character\Possession as CharacterPossession;
use App\Models\Character;
use App\Models\Possession;
use Illuminate\Database\Seeder;

class PossessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Character::query()->each(function (Character $character): void {
            foreach (CharacterPossession::cases() as $type) {
                $possession = Possession::factory()->create(['type' => $type]);

                $pivot = [];

                $modifier = fake()->optional()->numberBetween(-10, 10);
                if ($modifier) {
                    $pivot['modifier'] = $modifier;
                    $pivot['modifier_sign'] = fake()->optional()->randomElement(ModifierSign::cases())?->value;
                }
                $character->possessions()->attach($possession->id, $pivot);
            }
        });
    }
}

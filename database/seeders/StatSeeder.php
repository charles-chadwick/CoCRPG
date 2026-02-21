<?php

namespace Database\Seeders;

use App\Enums\Character\Stat as CharacterStat;
use App\Models\Character;
use App\Models\Stat;
use Illuminate\Database\Seeder;

class StatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Character::query()->each(function (Character $character): void {
            foreach (CharacterStat::cases() as $stat) {
                Stat::factory()->create([
                    'character_id' => $character->id,
                    'name' => $stat,
                ]);
            }
        });
    }
}

<?php

namespace App\Actions;

use App\Models\Character;
use App\Models\Stat;

class UpdateCharacterStatsAction
{
    public function handle(Character $character, array $stats): void
    {
        foreach ($stats as $statData) {
            Stat::updateOrCreate(
                ['character_id' => $character->id, 'name' => $statData['name']],
                ['value' => $statData['value']],
            );
        }
    }
}

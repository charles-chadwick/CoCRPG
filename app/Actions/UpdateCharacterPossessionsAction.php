<?php

namespace App\Actions;

use App\Models\Character;
use App\Models\Possession;

class UpdateCharacterPossessionsAction
{
    public function handle(Character $character, array $possessions, array $newPossessions): void
    {
        $syncData = [];

        foreach ($possessions as $item) {
            $syncData[$item['possession_id']] = [
                'modifier_sign' => $item['modifier_sign'] ?? null,
                'modifier' => $item['modifier'] ?? null,
            ];
        }

        foreach ($newPossessions as $item) {
            $possession = Possession::firstOrCreate(
                ['type' => $item['type'], 'name' => $item['name']],
                ['value' => $item['value']],
            );

            $syncData[$possession->id] = [
                'modifier_sign' => $item['modifier_sign'] ?? null,
                'modifier' => $item['modifier'] ?? null,
            ];
        }

        $character->possessions()->sync($syncData);
    }
}

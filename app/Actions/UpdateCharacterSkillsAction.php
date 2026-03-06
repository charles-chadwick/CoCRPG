<?php

namespace App\Actions;

use App\Models\Character;
use App\Models\CharacterSkill;

class UpdateCharacterSkillsAction
{
    public function handle(Character $character, array $skills): void
    {
        foreach ($skills as $skillData) {
            CharacterSkill::updateOrCreate(
                ['character_id' => $character->id, 'skill_id' => $skillData['skill_id']],
                ['value' => $skillData['value']],
            );
        }
    }
}

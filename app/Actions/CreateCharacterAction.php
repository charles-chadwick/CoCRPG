<?php

namespace App\Actions;

use App\Models\Character;
use App\Models\CharacterSkill;
use App\Models\Possession;
use App\Models\Stat;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateCharacterAction
{
    public function handle(User $user, array $data): Character
    {
        return DB::transaction(function () use ($user, $data): Character {
            $character = Character::create([
                'user_id' => $user->id,
                'campaign_id' => $data['campaign_id'] ?? null,
                'name' => $data['name'],
                'occupation' => $data['occupation'],
                'age' => $data['age'],
                'gender' => $data['gender'] ?? null,
                'birthplace' => $data['birthplace'] ?? null,
                'residence' => $data['residence'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            foreach ($data['stats'] as $statData) {
                Stat::create([
                    'character_id' => $character->id,
                    'name' => $statData['name'],
                    'value' => $statData['value'],
                ]);
            }

            foreach ($data['skills'] as $skillData) {
                CharacterSkill::create([
                    'character_id' => $character->id,
                    'skill_id' => $skillData['skill_id'],
                    'value' => $skillData['value'],
                ]);
            }

            $existingPossessions = collect($data['possessions'] ?? []);
            $newPossessions = collect($data['new_possessions'] ?? []);

            $allPossessionIds = [];

            foreach ($existingPossessions as $item) {
                $allPossessionIds[$item['possession_id']] = [
                    'modifier_sign' => $item['modifier_sign'] ?? null,
                    'modifier' => $item['modifier'] ?? null,
                ];
            }

            foreach ($newPossessions as $item) {
                $possession = Possession::firstOrCreate(
                    ['type' => $item['type'], 'name' => $item['name']],
                    ['value' => $item['value']],
                );

                $allPossessionIds[$possession->id] = [
                    'modifier_sign' => $item['modifier_sign'] ?? null,
                    'modifier' => $item['modifier'] ?? null,
                ];
            }

            if (! empty($allPossessionIds)) {
                $character->possessions()->sync($allPossessionIds);
            }

            return $character;
        });
    }
}

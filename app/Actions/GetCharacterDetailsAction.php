<?php

namespace App\Actions;

use App\Http\Resources\CharacterSkillResource;
use App\Http\Resources\StatResource;
use App\Models\Character;

class GetCharacterDetailsAction
{
    /**
     * Load a character with its relations and shape it for the front end.
     *
     * @return array{
     *     id: int,
     *     campaign_id: ?int,
     *     campaign: ?array{id: int, title: string},
     *     name: string,
     *     occupation: string,
     *     age: int,
     *     gender: ?string,
     *     birthplace: ?string,
     *     residence: ?string,
     *     description: ?string,
     *     stats: array<int, array<string, mixed>>,
     *     skills: array<int, array<string, mixed>>,
     *     possessions: array<string, array<int, array<string, mixed>>>
     * }
     */
    public function handle(Character $character): array
    {
        $character->load([
            'campaign:id,title',
            'stats',
            'characterSkills.skill',
            'possessions',
        ]);

        return [
            'id' => $character->id,
            'campaign_id' => $character->campaign_id,
            'campaign' => $character->campaign ? [
                'id' => $character->campaign->id,
                'title' => $character->campaign->title,
            ] : null,
            'name' => $character->name,
            'occupation' => $character->occupation->value,
            'age' => $character->age,
            'gender' => $character->gender,
            'birthplace' => $character->birthplace,
            'residence' => $character->residence,
            'description' => $character->description,
            'stats' => StatResource::collection($character->stats)->resolve(),
            'skills' => CharacterSkillResource::collection($character->characterSkills)->resolve(),
            'possessions' => $character->groupedPossessions(),
        ];
    }
}

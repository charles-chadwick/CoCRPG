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
            'stats',
            'characterSkills.skill',
            'possessions',
        ]);

        return [
            'id' => $character->id,
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

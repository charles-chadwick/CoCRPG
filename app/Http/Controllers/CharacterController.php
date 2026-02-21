<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Inertia\Inertia;
use Inertia\Response;

class CharacterController extends Controller
{
    public function show(Character $character): Response
    {
        $character->load(['stats', 'characterSkills', 'possessions']);

        return Inertia::render('Characters/Show', [
            'character' => [
                'id' => $character->id,
                'name' => $character->name,
                'occupation' => $character->occupation->value,
                'age' => $character->age,
                'gender' => $character->gender,
                'birthplace' => $character->birthplace,
                'residence' => $character->residence,
                'description' => $character->description,
                'stats' => $character->stats->map(fn ($stat) => [
                    'name' => $stat->name->value,
                    'value' => $stat->value,
                    'half' => $stat->halfValue(),
                    'fifth' => $stat->fifthValue(),
                ]),
                'skills' => $character->characterSkills->map(fn ($skill) => [
                    'name' => $skill->name,
                    'value' => $skill->value,
                ]),
                'possessions' => $character->possessions
                    ->groupBy(fn ($possession) => $possession->type->value)
                    ->map(fn ($group) => $group->map(fn ($possession) => [
                        'name' => $possession->name,
                        'value' => $possession->value,
                        'modifier_sign' => $possession->pivot->modifier_sign?->value,
                        'modifier' => $possession->pivot->modifier,
                    ])),
            ],
        ]);
    }
}

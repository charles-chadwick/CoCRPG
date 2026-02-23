<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Controllers;

use App\Enums\Character\Occupation;
use App\Http\Requests\UpdateCharacterRequest;
use App\Http\Resources\CharacterSkillResource;
use App\Http\Resources\StatResource;
use App\Models\Character;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CharacterController extends Controller
{
    public function show(Character $character): Response
    {
        $character->load([
            'stats',
            'characterSkills',
            'possessions',
        ]);

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
                'stats' => StatResource::collection($character->stats),
                'skills' => CharacterSkillResource::collection($character->characterSkills),
                'possessions' => $character->groupedPossessions(),
            ],
            'occupation_options' => collect(Occupation::cases())
                ->map(fn ($case) => [
                    'value' => $case->value,
                    'label' => $case->value,
                ])
                ->values(),
        ]);
    }

    public function update(UpdateCharacterRequest $request, Character $character): RedirectResponse
    {
        $character->update($request->validated());

        return redirect()->route('characters.show', $character);
    }
}

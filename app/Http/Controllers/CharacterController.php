<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Controllers;

use App\Actions\CreateCharacterAction;
use App\Actions\UpdateCharacterPossessionsAction;
use App\Actions\UpdateCharacterSkillsAction;
use App\Actions\UpdateCharacterStatsAction;
use App\Data\OccupationSkills;
use App\Enums\Character\Occupation;
use App\Enums\Character\Stat as StatEnum;
use App\Http\Requests\StoreCharacterRequest;
use App\Http\Requests\UpdateCharacterPossessionsRequest;
use App\Http\Requests\UpdateCharacterRequest;
use App\Http\Requests\UpdateCharacterSkillsRequest;
use App\Http\Requests\UpdateCharacterStatsRequest;
use App\Http\Resources\CharacterSkillResource;
use App\Http\Resources\StatResource;
use App\Models\Character;
use App\Models\Possession;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CharacterController extends Controller
{
    public function create(): Response
    {
        $allSkills = Skill::query()->get()->map(fn (Skill $skill) => [
            'id' => $skill->id,
            'name' => $skill->name->value,
            'base' => $skill->name->baseValue(),
        ])->values();

        $allPossessions = Possession::query()->get()
            ->groupBy(fn (Possession $p) => $p->type->value)
            ->map(fn ($group) => $group->map(fn (Possession $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'value' => $p->value,
                'type' => $p->type->value,
            ])->values())
            ->toArray();

        return Inertia::render('Characters/Create', [
            'occupation_options' => collect(Occupation::cases())
                ->map(fn ($case) => ['value' => $case->value, 'label' => $case->value])
                ->values(),
            'all_skills' => $allSkills,
            'all_possessions' => $allPossessions,
            'stat_names' => collect(StatEnum::cases())->map(fn ($s) => $s->value)->values(),
            'occupation_skills' => OccupationSkills::SKILLS,
        ]);
    }

    public function store(StoreCharacterRequest $request, CreateCharacterAction $action): RedirectResponse
    {
        $character = $action->handle(auth()->user(), $request->validated());

        return redirect()->route('characters.show', $character);
    }

    public function show(Character $character): Response
    {
        $character->load([
            'stats',
            'characterSkills.skill',
            'possessions',
        ]);

        $allSkills = Skill::query()->get()->map(fn (Skill $skill) => [
            'id' => $skill->id,
            'name' => $skill->name->value,
            'base' => $skill->name->baseValue(),
        ])->values();

        $allPossessions = Possession::query()->get()
            ->groupBy(fn (Possession $p) => $p->type->value)
            ->map(fn ($group) => $group->map(fn (Possession $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'value' => $p->value,
                'type' => $p->type->value,
            ])->values())
            ->toArray();

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
                'stats' => StatResource::collection($character->stats)->resolve(),
                'skills' => CharacterSkillResource::collection($character->characterSkills)->resolve(),
                'possessions' => $character->groupedPossessions(),
            ],
            'occupation_options' => collect(Occupation::cases())
                ->map(fn ($case) => ['value' => $case->value, 'label' => $case->value])
                ->values(),
            'all_skills' => $allSkills,
            'all_possessions' => $allPossessions,
            'occupation_skills' => OccupationSkills::SKILLS,
        ]);
    }

    public function update(UpdateCharacterRequest $request, Character $character): RedirectResponse
    {
        $character->update($request->validated());

        return redirect()->route('characters.show', $character);
    }

    public function updateStats(UpdateCharacterStatsRequest $request, Character $character, UpdateCharacterStatsAction $action): RedirectResponse
    {
        $action->handle($character, $request->validated()['stats']);

        return redirect()->route('characters.show', $character);
    }

    public function updateSkills(UpdateCharacterSkillsRequest $request, Character $character, UpdateCharacterSkillsAction $action): RedirectResponse
    {
        $action->handle($character, $request->validated()['skills']);

        return redirect()->route('characters.show', $character);
    }

    public function updatePossessions(UpdateCharacterPossessionsRequest $request, Character $character, UpdateCharacterPossessionsAction $action): RedirectResponse
    {
        $validated = $request->validated();

        $action->handle($character, $validated['possessions'] ?? [], $validated['new_possessions'] ?? []);

        return redirect()->route('characters.show', $character);
    }
}

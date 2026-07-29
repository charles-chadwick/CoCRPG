<?php

namespace App\Http\Controllers;

use App\Actions\CreateCharacterAction;
use App\Actions\GetCharacterDetailsAction;
use App\Actions\GetCharacterFormOptionsAction;
use App\Actions\UpdateCharacterAction;
use App\Actions\UpdateCharacterPossessionsAction;
use App\Actions\UpdateCharacterSkillsAction;
use App\Actions\UpdateCharacterStatsAction;
use App\Http\Requests\StoreCharacterRequest;
use App\Http\Requests\UpdateCharacterPossessionsRequest;
use App\Http\Requests\UpdateCharacterRequest;
use App\Http\Requests\UpdateCharacterSkillsRequest;
use App\Http\Requests\UpdateCharacterStatsRequest;
use App\Models\Character;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CharacterController extends Controller
{
    public function create(GetCharacterFormOptionsAction $formOptions): Response
    {
        return Inertia::render('Characters/Create', $formOptions->handle());
    }

    public function store(StoreCharacterRequest $request, CreateCharacterAction $action): RedirectResponse
    {
        $character = $action->handle(auth()->user(), $request->validated());

        return redirect()->route('characters.show', $character);
    }

    public function show(Character $character, GetCharacterDetailsAction $details, GetCharacterFormOptionsAction $formOptions): Response
    {
        return Inertia::render('Characters/Show', [
            ...$formOptions->handle(),
            'character' => $details->handle($character),
        ]);
    }

    public function update(UpdateCharacterRequest $request, Character $character, UpdateCharacterAction $action): RedirectResponse
    {
        $action->handle($character, $request->validated());

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

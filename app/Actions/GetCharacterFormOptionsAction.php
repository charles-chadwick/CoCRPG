<?php

namespace App\Actions;

use App\Data\OccupationSkills;
use App\Enums\Character\Occupation;
use App\Enums\Character\Stat as StatEnum;
use App\Models\Possession;
use App\Models\Skill;
use Illuminate\Support\Collection;

class GetCharacterFormOptionsAction
{
    /**
     * Build the shared option lists used by the character create and show screens.
     *
     * @return array{
     *     occupation_options: Collection<int, array{value: string, label: string}>,
     *     all_skills: Collection<int, array{id: int, name: string, base: int}>,
     *     all_possessions: array<string, array<int, array{id: int, name: string, value: string, type: string}>>,
     *     stat_names: Collection<int, string>,
     *     occupation_skills: array<string, array<int, string>>
     * }
     */
    public function handle(): array
    {
        return [
            'occupation_options' => $this->occupationOptions(),
            'all_skills' => $this->allSkills(),
            'all_possessions' => $this->allPossessions(),
            'stat_names' => $this->statNames(),
            'occupation_skills' => OccupationSkills::SKILLS,
        ];
    }

    /**
     * @return Collection<int, array{value: string, label: string}>
     */
    private function occupationOptions(): Collection
    {
        return collect(Occupation::cases())
            ->map(fn (Occupation $occupation) => [
                'value' => $occupation->value,
                'label' => $occupation->value,
            ])
            ->values();
    }

    /**
     * @return Collection<int, array{id: int, name: string, base: int}>
     */
    private function allSkills(): Collection
    {
        return Skill::query()->get()
            ->map(fn (Skill $skill) => [
                'id' => $skill->id,
                'name' => $skill->name->value,
                'base' => $skill->name->baseValue(),
            ])
            ->values();
    }

    /**
     * @return array<string, array<int, array{id: int, name: string, value: string, type: string}>>
     */
    private function allPossessions(): array
    {
        return Possession::query()->get()
            ->groupBy(fn (Possession $possession) => $possession->type->value)
            ->map(fn (Collection $group) => $group->map(fn (Possession $possession) => [
                'id' => $possession->id,
                'name' => $possession->name,
                'value' => $possession->value,
                'type' => $possession->type->value,
            ])->values())
            ->toArray();
    }

    /**
     * @return Collection<int, string>
     */
    private function statNames(): Collection
    {
        return collect(StatEnum::cases())
            ->map(fn (StatEnum $stat) => $stat->value)
            ->values();
    }
}

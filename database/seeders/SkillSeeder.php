<?php

namespace Database\Seeders;

use App\Data\OccupationSkills;
use App\Enums\Character\Skill;
use App\Enums\Character\Stat as CharacterStat;
use App\Models\Character;
use App\Models\CharacterSkill;
use App\Models\Skill as SkillModel;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        // Seed the skills reference table and build a name → id map
        $skillMap = [];
        foreach (Skill::cases() as $skillEnum) {
            $skill = SkillModel::firstOrCreate(['name' => $skillEnum]);
            $skillMap[$skillEnum->value] = $skill->id;
        }

        Character::query()->with('stats')->each(function (Character $character) use ($skillMap): void {
            $edu = $character->stats->firstWhere('name', CharacterStat::Education)?->value ?? 50;
            $int = $character->stats->firstWhere('name', CharacterStat::Intelligence)?->value ?? 50;
            $dex = $character->stats->firstWhere('name', CharacterStat::Dexterity)?->value ?? 50;

            // Build base values for every skill
            $skillValues = [];
            foreach (Skill::cases() as $skill) {
                $skillValues[$skill->value] = match ($skill) {
                    Skill::Dodge => intdiv($dex, 2),
                    Skill::LanguageOwn => $edu,
                    default => $skill->baseValue(),
                };
            }

            // Occupation skill points: EDU × 4, distributed across 8 occupation skills
            $occupationPool = OccupationSkills::SKILLS[$character->occupation->value] ?? [];
            $skillValues = $this->distributePoints($skillValues, $occupationPool, $edu * 4);

            // Personal interest points: INT × 2, distributed across any 4 skills freely
            $interestPool = collect(Skill::cases())
                ->reject(fn (Skill $s) => $s === Skill::CthulhuMythos)
                ->map(fn (Skill $s) => $s->value)
                ->shuffle()
                ->take(4)
                ->all();
            $skillValues = $this->distributePoints($skillValues, $interestPool, $int * 2);

            // Persist character skills using skill_id references
            foreach ($skillValues as $name => $value) {
                CharacterSkill::create([
                    'character_id' => $character->id,
                    'skill_id' => $skillMap[$name],
                    'value' => min(99, $value),
                ]);
            }
        });
    }

    /**
     * Distribute points across a skill pool, 1–20 points at a time per skill.
     *
     * @param  array<string, int>  $skillValues
     * @param  list<string>  $pool
     * @return array<string, int>
     */
    private function distributePoints(array $skillValues, array $pool, int $points): array
    {
        if (empty($pool) || $points <= 0) {
            return $skillValues;
        }

        while ($points > 0) {
            $skill = $pool[array_rand($pool)];
            $current = $skillValues[$skill] ?? 0;

            if ($current >= 99) {
                continue;
            }

            $add = rand(1, min($points, 20, 99 - $current));
            $skillValues[$skill] = $current + $add;
            $points -= $add;
        }

        return $skillValues;
    }
}

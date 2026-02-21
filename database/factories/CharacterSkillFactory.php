<?php

namespace Database\Factories;

use App\Enums\Character\Skill as SkillEnum;
use App\Models\Character;
use App\Models\CharacterSkill;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CharacterSkill>
 */
class CharacterSkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skillEnum = fake()->randomElement(SkillEnum::cases());
        $skill = Skill::firstOrCreate(['name' => $skillEnum]);

        return [
            'character_id' => Character::factory(),
            'skill_id' => $skill->id,
            'value' => fake()->numberBetween($skillEnum->baseValue(), min(99, $skillEnum->baseValue() + 40)),
        ];
    }
}

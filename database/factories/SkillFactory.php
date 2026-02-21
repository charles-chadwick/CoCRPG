<?php

namespace Database\Factories;

use App\Enums\Character\Skill as SkillEnum;
use App\Models\Character;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skill = fake()->randomElement(SkillEnum::cases());

        return [
            'character_id' => Character::factory(),
            'name'         => $skill,
            'value'        => fake()->numberBetween($skill->baseValue(), min(99, $skill->baseValue() + 40)),
        ];
    }
}

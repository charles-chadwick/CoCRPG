<?php

namespace Database\Factories;

use App\Enums\CharacterStat as StatEnum;
use App\Models\Character;
use App\Models\Stat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stat>
 */
class StatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'character_id' => Character::factory(),
            'name' => fake()->randomElement(StatEnum::cases()),
            'value' => fake()->numberBetween(15, 90),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enums\CharacterClass;
use App\Enums\Race;
use App\Models\Character;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Character>
 */
class CharacterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $level = fake()->numberBetween(1, 20);

        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'race' => fake()->randomElement(Race::cases()),
            'character_class' => fake()->randomElement(CharacterClass::cases()),
            'level' => $level,
            'experience_points' => $level * fake()->numberBetween(100, 1000),
        ];
    }
}

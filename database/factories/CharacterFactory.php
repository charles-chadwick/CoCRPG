<?php

namespace Database\Factories;

use App\Enums\Character\Occupation;
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
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'occupation' => fake()->randomElement(Occupation::cases()),
            'age' => fake()->numberBetween(15, 70),
            'gender' => fake()->optional()->randomElement(['Male', 'Female', 'Non-binary']),
            'birthplace' => fake()->city() . ', ' . fake()->country(),
            'residence' => fake()->city() . ', ' . fake()->country(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enums\PlayerPossession;
use App\Models\Possession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Possession>
 */
class PossessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(PlayerPossession::cases()),
            'name' => fake()->word(),
            'value' => fake()->numberBetween(1, 100),
        ];
    }
}

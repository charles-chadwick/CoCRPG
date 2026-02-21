<?php

namespace Database\Factories;

use App\Enums\ModifierSign;
use App\Enums\PlayerPossession;
use App\Models\Player;
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
            'player_id' => Player::factory(),
            'name' => fake()->randomElement(PlayerPossession::cases()),
            'value' => fake()->numberBetween(1, 100),
            'modifier_sign' => fake()->optional()->randomElement(ModifierSign::cases()),
            'modifier' => fake()->optional()->numberBetween(1, 5),
        ];
    }
}

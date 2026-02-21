<?php

namespace Database\Factories;

use App\Enums\PlayerStat as StatEnum;
use App\Models\Player;
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
            'player_id' => Player::factory(),
            'name' => fake()->randomElement(StatEnum::cases()),
            'value' => fake()->numberBetween(1, 20),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enums\Campaign\SessionStatus;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CampaignSession>
 */
class CampaignSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory(),
            'title' => fake()->sentence(3),
            'scheduled_at' => fake()->dateTimeBetween('+1 week', '+3 months'),
            'status' => SessionStatus::Scheduled->value,
            'notes' => null,
        ];
    }

    public function played(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_at' => fake()->dateTimeBetween('-3 months', '-1 week'),
            'status' => SessionStatus::Played->value,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SessionStatus::Cancelled->value,
        ]);
    }
}

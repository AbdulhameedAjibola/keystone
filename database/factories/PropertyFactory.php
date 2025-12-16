<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Agent;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agent_id' => Agent::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 100000, 50000000),
            'property_type' => $this->faker->randomElement(['apartment','house','shortlet','penthouse','land','commercial']),
            'listing_type' => $this->faker->randomElement(['sale','rent']),
            'status' => $this->faker->randomElement(['available', 'sold', 'unavailable']),
            'bedrooms' => $this->faker->numberBetween(1, 6),
            'bathrooms' => $this->faker->numberBetween(1, 4),
            'size' => $this->faker->numberBetween(200, 3000),
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Career>
 */
class CareerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // admin user
            'title' => $this->faker->jobTitle,
            'location' => $this->faker->city,
            'type' => $this->faker->randomElement(['full-time','part-time','contract', 'internship']),
            'description' => $this->faker->paragraph,
            'requirements' => $this->faker->paragraph,
            'salary' => $this->faker->numberBetween(50000, 500000) . ' NGN',
            'is_active' => true,
            'application_deadline' => $this->faker->optional()->date(),
        ];
    }
}

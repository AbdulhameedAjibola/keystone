<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Property;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'public_id' => $this->faker->uuid,
            'url' => $this->faker->imageUrl(),
            'type' => $this->faker->randomElement(['image', 'video', 'document']),
            'format' => 'jpg',
            'size' => $this->faker->numberBetween(200, 2000),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->sentence(3);
        return [
            'name' => ['en' => $name, 'id' => $name],
            'slug' => Str::slug($name),
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(8),
            'venue' => ['en' => $this->faker->address, 'id' => $this->faker->address],
            'is_active' => true,
            'status' => 'upcoming',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'project_date' => fake()->dateTimeBetween('-1 month', '+3 months')->format('Y-m-d'),
            'status' => fake()->randomElement(['pending', 'in_progress', 'completed']),
        ];
    }
}

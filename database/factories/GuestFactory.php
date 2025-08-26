<?php

namespace Database\Factories;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guest>
 */
class GuestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'id_number' => fake()->optional()->numerify('############'),
            'date_of_birth' => fake()->optional()->date('Y-m-d', '2000-01-01'),
            'id_type' => fake()->optional()->randomElement(['passport', 'drivers_license', 'national_id', 'other']),
        ];
    }
}
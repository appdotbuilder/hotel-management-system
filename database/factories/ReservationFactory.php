<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Guest;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkInDate = fake()->dateTimeBetween('-30 days', '+60 days');
        $checkOutDate = fake()->dateTimeBetween($checkInDate, $checkInDate->format('Y-m-d') . ' +14 days');
        
        // Calculate nights for pricing
        $nights = $checkOutDate->diff($checkInDate)->days;
        
        $adults = fake()->numberBetween(1, 4);
        $children = fake()->numberBetween(0, 3);

        return [
            'confirmation_number' => 'RES-' . strtoupper(fake()->unique()->bothify('????####')),
            'guest_id' => Guest::factory(),
            'room_id' => Room::factory(),
            'check_in_date' => $checkInDate->format('Y-m-d'),
            'check_out_date' => $checkOutDate->format('Y-m-d'),
            'adults' => $adults,
            'children' => $children,
            'total_amount' => fake()->randomFloat(2, $nights * 80, $nights * 500), // Will be recalculated
            'status' => fake()->randomElement(['confirmed', 'checked_in', 'checked_out', 'cancelled']),
            'special_requests' => fake()->optional()->paragraph(),
            'checked_in_at' => fake()->optional()->dateTimeBetween($checkInDate, 'now'),
            'checked_out_at' => fake()->optional()->dateTimeBetween($checkInDate, 'now'),
        ];
    }

    /**
     * Create a confirmed reservation.
     */
    public function confirmed(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'checked_in_at' => null,
            'checked_out_at' => null,
        ]);
    }

    /**
     * Create a checked-in reservation.
     */
    public function checkedIn(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'checked_in',
            'checked_in_at' => fake()->dateTimeBetween('-7 days', 'now'),
            'checked_out_at' => null,
        ]);
    }
}
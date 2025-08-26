<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['single', 'double', 'suite', 'deluxe', 'presidential'];
        $type = fake()->randomElement($types);
        
        $basePrices = [
            'single' => [80, 120],
            'double' => [120, 180],
            'suite' => [200, 300],
            'deluxe' => [300, 450],
            'presidential' => [500, 800],
        ];

        $capacities = [
            'single' => 1,
            'double' => 2,
            'suite' => 4,
            'deluxe' => 6,
            'presidential' => 8,
        ];

        $amenities = [
            'Wi-Fi',
            'Air Conditioning',
            'Mini Bar',
            'Room Service',
            'Balcony',
            'Ocean View',
            'City View',
            'Jacuzzi',
            'Fireplace',
            'Kitchen',
            'Living Area',
            'Work Desk',
            'Safe',
            'Coffee Maker',
        ];

        return [
            'room_number' => fake()->unique()->numerify('###'),
            'type' => $type,
            'price_per_night' => fake()->randomFloat(2, $basePrices[$type][0], $basePrices[$type][1]),
            'status' => fake()->randomElement(['available', 'occupied', 'maintenance', 'cleaning']),
            'capacity' => $capacities[$type],
            'amenities' => fake()->randomElements($amenities, random_int(3, 8)),
            'description' => fake()->paragraph(3),
        ];
    }

    /**
     * Create an available room.
     */
    public function available(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'available',
        ]);
    }

    /**
     * Create an occupied room.
     */
    public function occupied(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'occupied',
        ]);
    }
}
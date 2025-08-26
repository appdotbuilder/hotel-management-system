<?php

namespace Database\Factories;

use App\Models\HousekeepingTask;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HousekeepingTask>
 */
class HousekeepingTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $taskDescriptions = [
            'cleaning' => [
                'Deep clean bathroom and replace towels',
                'Vacuum carpets and mop floors',
                'Change bed linens and make beds',
                'Clean windows and dust furniture',
                'Restock minibar and amenities'
            ],
            'maintenance' => [
                'Fix leaky faucet in bathroom',
                'Replace burnt out light bulbs',
                'Repair air conditioning unit',
                'Fix squeaky door hinges',
                'Replace broken window handle'
            ],
            'inspection' => [
                'Inspect room for damages',
                'Check all electrical outlets',
                'Verify all amenities are working',
                'Check plumbing for leaks',
                'Inspect furniture for wear and tear'
            ],
            'inventory' => [
                'Count and restock towels',
                'Check minibar inventory',
                'Verify room supplies',
                'Update amenity checklist',
                'Count bed linens and pillows'
            ]
        ];

        $taskType = fake()->randomElement(['cleaning', 'maintenance', 'inspection', 'inventory']);
        
        return [
            'room_id' => Room::factory(),
            'assigned_to' => fake()->optional()->randomElement([1, null]), // Assuming user ID 1 exists
            'task_type' => $taskType,
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => fake()->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
            'description' => fake()->randomElement($taskDescriptions[$taskType]),
            'scheduled_at' => fake()->optional()->dateTimeBetween('now', '+7 days'),
            'started_at' => fake()->optional()->dateTimeBetween('-2 days', 'now'),
            'completed_at' => fake()->optional()->dateTimeBetween('-1 day', 'now'),
            'completion_notes' => fake()->optional()->paragraph(),
        ];
    }

    /**
     * Create a pending task.
     */
    public function pending(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'started_at' => null,
            'completed_at' => null,
            'completion_notes' => null,
        ]);
    }

    /**
     * Create a high priority task.
     */
    public function highPriority(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }
}
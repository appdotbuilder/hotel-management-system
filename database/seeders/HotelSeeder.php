<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\HousekeepingTask;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo user if it doesn't exist
        if (!User::where('email', 'admin@hotel.com')->exists()) {
            User::create([
                'name' => 'Hotel Admin',
                'email' => 'admin@hotel.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        // Create rooms (50 rooms with various types)
        $this->command->info('Creating rooms...');
        
        // Create specific room types
        Room::factory(15)->available()->create(['type' => 'single']);
        Room::factory(20)->available()->create(['type' => 'double']);
        Room::factory(8)->available()->create(['type' => 'suite']);
        Room::factory(5)->available()->create(['type' => 'deluxe']);
        Room::factory(2)->available()->create(['type' => 'presidential']);
        
        // Create some occupied and maintenance rooms
        Room::factory(8)->occupied()->create();
        Room::factory(4)->create(['status' => 'maintenance']);
        Room::factory(3)->create(['status' => 'cleaning']);

        // Create guests
        $this->command->info('Creating guests...');
        Guest::factory(80)->create();

        // Create reservations with realistic data
        $this->command->info('Creating reservations...');
        
        $rooms = Room::all();
        $guests = Guest::all();
        
        // Create past reservations (checked out)
        for ($i = 0; $i < 25; $i++) {
            $room = $rooms->random();
            $guest = $guests->random();
            
            $checkIn = Carbon::now()->subDays(random_int(30, 90));
            $checkOut = $checkIn->copy()->addDays(random_int(1, 7));
            $nights = $checkOut->diffInDays($checkIn);
            
            Reservation::create([
                'confirmation_number' => 'RES-' . strtoupper(uniqid()),
                'guest_id' => $guest->id,
                'room_id' => $room->id,
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'adults' => random_int(1, 3),
                'children' => random_int(0, 2),
                'total_amount' => $room->price_per_night * $nights,
                'status' => 'checked_out',
                'checked_in_at' => $checkIn,
                'checked_out_at' => $checkOut,
            ]);
        }

        // Create current reservations (checked in)
        $occupiedRooms = Room::where('status', 'occupied')->get();
        foreach ($occupiedRooms as $room) {
            $guest = $guests->random();
            
            $checkIn = Carbon::now()->subDays(random_int(0, 5));
            $checkOut = $checkIn->copy()->addDays(random_int(2, 10));
            $nights = $checkOut->diffInDays($checkIn);
            
            Reservation::create([
                'confirmation_number' => 'RES-' . strtoupper(uniqid()),
                'guest_id' => $guest->id,
                'room_id' => $room->id,
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'adults' => random_int(1, 3),
                'children' => random_int(0, 2),
                'total_amount' => $room->price_per_night * $nights,
                'status' => 'checked_in',
                'checked_in_at' => $checkIn,
            ]);
        }

        // Create future reservations (confirmed)
        for ($i = 0; $i < 15; $i++) {
            $room = Room::where('status', 'available')->inRandomOrder()->first();
            $guest = $guests->random();
            
            $checkIn = Carbon::now()->addDays(random_int(1, 30));
            $checkOut = $checkIn->copy()->addDays(random_int(1, 7));
            $nights = $checkOut->diffInDays($checkIn);
            
            Reservation::create([
                'confirmation_number' => 'RES-' . strtoupper(uniqid()),
                'guest_id' => $guest->id,
                'room_id' => $room->id,
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'adults' => random_int(1, 3),
                'children' => random_int(0, 2),
                'total_amount' => $room->price_per_night * $nights,
                'status' => 'confirmed',
                'special_requests' => collect([
                    'Late check-in requested',
                    'Ground floor room preferred',
                    'Extra towels needed',
                    'Quiet room away from elevator',
                    'Ocean view if available',
                    null, null, null // More likely to have no special requests
                ])->random(),
            ]);
        }

        // Create some reservations for today's check-ins and check-outs
        for ($i = 0; $i < 3; $i++) {
            $room = Room::where('status', 'available')->inRandomOrder()->first();
            $guest = $guests->random();
            
            $checkOut = Carbon::now()->addDays(random_int(2, 5));
            $nights = $checkOut->diffInDays(Carbon::now());
            
            Reservation::create([
                'confirmation_number' => 'RES-' . strtoupper(uniqid()),
                'guest_id' => $guest->id,
                'room_id' => $room->id,
                'check_in_date' => Carbon::now()->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'adults' => random_int(1, 3),
                'children' => random_int(0, 2),
                'total_amount' => $room->price_per_night * $nights,
                'status' => 'confirmed',
            ]);
        }

        // Create today's check-outs
        for ($i = 0; $i < 2; $i++) {
            $room = Room::where('status', 'occupied')->inRandomOrder()->first();
            if ($room) {
                $guest = $guests->random();
                
                $checkIn = Carbon::now()->subDays(random_int(1, 5));
                $nights = Carbon::now()->diffInDays($checkIn);
                
                Reservation::create([
                    'confirmation_number' => 'RES-' . strtoupper(uniqid()),
                    'guest_id' => $guest->id,
                    'room_id' => $room->id,
                    'check_in_date' => $checkIn->toDateString(),
                    'check_out_date' => Carbon::now()->toDateString(),
                    'adults' => random_int(1, 3),
                    'children' => random_int(0, 2),
                    'total_amount' => $room->price_per_night * $nights,
                    'status' => 'checked_in',
                    'checked_in_at' => $checkIn,
                ]);
            }
        }

        // Create housekeeping tasks
        $this->command->info('Creating housekeeping tasks...');
        
        // Tasks for rooms that need cleaning
        $cleaningRooms = Room::where('status', 'cleaning')->get();
        foreach ($cleaningRooms as $room) {
            HousekeepingTask::create([
                'room_id' => $room->id,
                'assigned_to' => null,
                'task_type' => 'cleaning',
                'priority' => 'medium',
                'status' => 'pending',
                'description' => 'Post-checkout cleaning required',
                'scheduled_at' => Carbon::now()->addHours(random_int(1, 4)),
            ]);
        }

        // Tasks for maintenance rooms
        $maintenanceRooms = Room::where('status', 'maintenance')->get();
        foreach ($maintenanceRooms as $room) {
            HousekeepingTask::create([
                'room_id' => $room->id,
                'assigned_to' => null,
                'task_type' => 'maintenance',
                'priority' => collect(['high', 'urgent'])->random(),
                'status' => 'pending',
                'description' => collect([
                    'Air conditioning not working properly',
                    'Bathroom faucet needs repair',
                    'Light fixture replacement needed',
                    'Window lock repair required'
                ])->random(),
                'scheduled_at' => Carbon::now()->addHours(random_int(1, 8)),
            ]);
        }

        // Random additional tasks
        HousekeepingTask::factory(10)->pending()->create();
        HousekeepingTask::factory(5)->highPriority()->create();

        $this->command->info('Hotel seeding completed!');
    }
}
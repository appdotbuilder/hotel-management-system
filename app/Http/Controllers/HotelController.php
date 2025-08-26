<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Guest;
use App\Models\HousekeepingTask;
use Inertia\Inertia;

class HotelController extends Controller
{
    /**
     * Display the hotel management dashboard.
     */
    public function index()
    {
        // Get dashboard statistics
        $stats = [
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', 'available')->count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'maintenance_rooms' => Room::whereIn('status', ['maintenance', 'cleaning'])->count(),
            'total_reservations' => Reservation::count(),
            'active_reservations' => Reservation::whereIn('status', ['confirmed', 'checked_in'])->count(),
            'todays_checkins' => Reservation::where('check_in_date', now()->toDateString())->count(),
            'todays_checkouts' => Reservation::where('check_out_date', now()->toDateString())->count(),
            'pending_tasks' => HousekeepingTask::where('status', 'pending')->count(),
            'total_guests' => Guest::count(),
        ];

        // Get recent reservations
        $recent_reservations = Reservation::with(['guest', 'room'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get upcoming check-ins
        $upcoming_checkins = Reservation::with(['guest', 'room'])
            ->where('check_in_date', '>=', now()->toDateString())
            ->where('status', 'confirmed')
            ->orderBy('check_in_date')
            ->limit(5)
            ->get();

        // Get pending housekeeping tasks
        $pending_tasks = HousekeepingTask::with(['room'])
            ->where('status', 'pending')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        return Inertia::render('welcome', [
            'stats' => $stats,
            'recent_reservations' => $recent_reservations,
            'upcoming_checkins' => $upcoming_checkins,
            'pending_tasks' => $pending_tasks,
        ]);
    }
}
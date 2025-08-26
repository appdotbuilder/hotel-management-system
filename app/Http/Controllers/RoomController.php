<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Room;
use Inertia\Inertia;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::with(['reservations' => function($query) {
            $query->where('status', 'confirmed')
                  ->orWhere('status', 'checked_in');
        }])
        ->paginate(12);
        
        return Inertia::render('rooms/index', [
            'rooms' => $rooms,
            'filters' => request()->only(['type', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('rooms/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        $room = Room::create($request->validated());

        return redirect()->route('rooms.show', $room)
            ->with('success', 'Room created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        $room->load(['reservations.guest', 'housekeepingTasks.assignedTo']);
        
        return Inertia::render('rooms/show', [
            'room' => $room,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        return Inertia::render('rooms/edit', [
            'room' => $room,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, Room $room)
    {
        $room->update($request->validated());

        return redirect()->route('rooms.show', $room)
            ->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        // Check if room has active reservations
        $activeReservations = $room->reservations()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();

        if ($activeReservations > 0) {
            return redirect()->route('rooms.index')
                ->with('error', 'Cannot delete room with active reservations.');
        }

        $room->delete();

        return redirect()->route('rooms.index')
            ->with('success', 'Room deleted successfully.');
    }
}
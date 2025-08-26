<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuestRequest;
use App\Models\Guest;
use Inertia\Inertia;

class GuestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $guests = Guest::with(['reservations'])
            ->orderBy('first_name')
            ->paginate(15);
        
        return Inertia::render('guests/index', [
            'guests' => $guests,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('guests/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGuestRequest $request)
    {
        $guest = Guest::create($request->validated());

        return redirect()->route('guests.show', $guest)
            ->with('success', 'Guest registered successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Guest $guest)
    {
        $guest->load(['reservations.room']);
        
        return Inertia::render('guests/show', [
            'guest' => $guest,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guest $guest)
    {
        return Inertia::render('guests/edit', [
            'guest' => $guest,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreGuestRequest $request, Guest $guest)
    {
        $guest->update($request->validated());

        return redirect()->route('guests.show', $guest)
            ->with('success', 'Guest information updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guest $guest)
    {
        // Check if guest has active reservations
        $activeReservations = $guest->reservations()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->count();

        if ($activeReservations > 0) {
            return redirect()->route('guests.index')
                ->with('error', 'Cannot delete guest with active reservations.');
        }

        $guest->delete();

        return redirect()->route('guests.index')
            ->with('success', 'Guest deleted successfully.');
    }
}
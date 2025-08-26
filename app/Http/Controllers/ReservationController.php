<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Guest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservation::with(['guest', 'room'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return Inertia::render('reservations/index', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rooms = Room::available()->get();
        $guests = Guest::orderBy('first_name')->get();
        
        return Inertia::render('reservations/create', [
            'rooms' => $rooms,
            'guests' => $guests,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request)
    {
        $validated = $request->validated();
        
        // Generate confirmation number
        $validated['confirmation_number'] = 'RES-' . strtoupper(uniqid());
        
        // Calculate total amount based on room price and nights
        $room = Room::findOrFail($validated['room_id']);
        $checkIn = \Carbon\Carbon::parse($validated['check_in_date']);
        $checkOut = \Carbon\Carbon::parse($validated['check_out_date']);
        $nights = $checkOut->diffInDays($checkIn);
        
        $validated['total_amount'] = $room->price_per_night * $nights;
        $validated['children'] = $validated['children'] ?? 0;
        
        $reservation = Reservation::create($validated);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Reservation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        $reservation->load(['guest', 'room']);
        
        return Inertia::render('reservations/show', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        $rooms = Room::available()->orWhere('id', $reservation->room_id)->get();
        $guests = Guest::orderBy('first_name')->get();
        
        return Inertia::render('reservations/edit', [
            'reservation' => $reservation,
            'rooms' => $rooms,
            'guests' => $guests,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreReservationRequest $request, Reservation $reservation)
    {
        $validated = $request->validated();
        
        // Recalculate total amount if dates or room changed
        $room = Room::findOrFail($validated['room_id']);
        $checkIn = \Carbon\Carbon::parse($validated['check_in_date']);
        $checkOut = \Carbon\Carbon::parse($validated['check_out_date']);
        $nights = $checkOut->diffInDays($checkIn);
        
        $validated['total_amount'] = $room->price_per_night * $nights;
        $validated['children'] = $validated['children'] ?? 0;
        
        $reservation->update($validated);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Reservation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        // Update reservation status to cancelled instead of deleting
        $reservation->update(['status' => 'cancelled']);

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation cancelled successfully.');
    }


}
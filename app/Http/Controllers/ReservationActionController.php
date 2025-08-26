<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationActionController extends Controller
{
    /**
     * Handle reservation actions (check-in, check-out, cancel).
     */
    public function store(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'action' => 'required|in:checkin,checkout,cancel',
        ]);

        $reservation = Reservation::with(['room'])->findOrFail($request->reservation_id);
        $message = '';

        switch ($request->action) {
            case 'checkin':
                $reservation->update([
                    'status' => 'checked_in',
                    'checked_in_at' => now(),
                ]);
                $reservation->room->update(['status' => 'occupied']);
                $message = 'Guest checked in successfully.';
                break;
                
            case 'checkout':
                $reservation->update([
                    'status' => 'checked_out',
                    'checked_out_at' => now(),
                ]);
                $reservation->room->update(['status' => 'cleaning']);
                $message = 'Guest checked out successfully.';
                break;
                
            case 'cancel':
                $reservation->update(['status' => 'cancelled']);
                if ($reservation->room->status === 'occupied') {
                    $reservation->room->update(['status' => 'available']);
                }
                $message = 'Reservation cancelled successfully.';
                break;
        }

        return redirect()->route('home')
            ->with('success', $message);
    }
}
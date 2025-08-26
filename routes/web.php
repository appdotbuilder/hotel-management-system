<?php

use App\Http\Controllers\HotelController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReservationActionController;
use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;

Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
})->name('health-check');

// Home page - Hotel Management Dashboard
Route::get('/', [HotelController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard route
    Route::get('dashboard', [HotelController::class, 'index'])->name('dashboard');
    
    // Resource routes for hotel management
    Route::resource('rooms', RoomController::class);
    Route::resource('reservations', ReservationController::class);
    Route::resource('guests', GuestController::class);
    
    // Quick actions for reservations (check-in, check-out, cancel)
    Route::post('/reservation-actions', [ReservationActionController::class, 'store'])->name('reservation.actions');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

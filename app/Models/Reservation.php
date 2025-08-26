<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Reservation
 *
 * @property int $id
 * @property string $confirmation_number
 * @property int $guest_id
 * @property int $room_id
 * @property \Illuminate\Support\Carbon $check_in_date
 * @property \Illuminate\Support\Carbon $check_out_date
 * @property int $adults
 * @property int $children
 * @property float $total_amount
 * @property string $status
 * @property string|null $special_requests
 * @property \Illuminate\Support\Carbon|null $checked_in_at
 * @property \Illuminate\Support\Carbon|null $checked_out_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Guest $guest
 * @property-read \App\Models\Room $room
 * @property-read int $nights
 * @property-read int $total_guests
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereConfirmationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereGuestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCheckInDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCheckOutDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereAdults($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereChildren($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereSpecialRequests($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCheckedInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCheckedOutAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation active()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation upcoming()
 * @method static \Database\Factories\ReservationFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Reservation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'confirmation_number',
        'guest_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'adults',
        'children',
        'total_amount',
        'status',
        'special_requests',
        'checked_in_at',
        'checked_out_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'adults' => 'integer',
        'children' => 'integer',
        'total_amount' => 'decimal:2',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the guest that owns the reservation.
     *
     * @return BelongsTo
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /**
     * Get the room that belongs to the reservation.
     *
     * @return BelongsTo
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Scope a query to only include active reservations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['confirmed', 'checked_in']);
    }

    /**
     * Scope a query to only include upcoming reservations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('check_in_date', '>=', now()->toDateString());
    }

    /**
     * Get the number of nights for the reservation.
     *
     * @return int
     */
    public function getNightsAttribute(): int
    {
        return (int) $this->check_out_date->diffInDays($this->check_in_date);
    }

    /**
     * Get the total number of guests.
     *
     * @return int
     */
    public function getTotalGuestsAttribute(): int
    {
        return $this->adults + $this->children;
    }
}
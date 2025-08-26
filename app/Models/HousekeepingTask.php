<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\HousekeepingTask
 *
 * @property int $id
 * @property int $room_id
 * @property int|null $assigned_to
 * @property string $task_type
 * @property string $priority
 * @property string $status
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $scheduled_at
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property string|null $completion_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Room $room
 * @property-read \App\Models\User|null $assignedTo
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask query()
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask whereTaskType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask whereScheduledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask whereCompletionNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask pending()
 * @method static \Illuminate\Database\Eloquent\Builder|HousekeepingTask highPriority()
 * @method static \Database\Factories\HousekeepingTaskFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class HousekeepingTask extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'room_id',
        'assigned_to',
        'task_type',
        'priority',
        'status',
        'description',
        'scheduled_at',
        'started_at',
        'completed_at',
        'completion_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the room that belongs to the task.
     *
     * @return BelongsTo
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the user assigned to the task.
     *
     * @return BelongsTo
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope a query to only include pending tasks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include high priority tasks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }
}
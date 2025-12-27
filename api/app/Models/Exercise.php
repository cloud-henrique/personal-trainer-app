<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Exercise Model
 *
 * Represents an exercise within a workout plan.
 * Uses BelongsToTenant trait for automatic multi-tenancy filtering.
 */
class Exercise extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'workout_id',
        'order',
        'name',
        'muscle_group',
        'description',
        'video_url',
        'sets',
        'reps',
        'rest',
        'load',
        'tempo',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order' => 'integer',
        'sets' => 'integer',
    ];

    /**
     * Get the workout that owns this exercise.
     */
    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    /**
     * Get all workout logs for this exercise.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(WorkoutLog::class);
    }
}

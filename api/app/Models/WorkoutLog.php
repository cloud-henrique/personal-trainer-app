<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * WorkoutLog Model
 *
 * Represents a log entry for a workout exercise execution.
 * Used to track student performance over time.
 * Uses BelongsToTenant trait for automatic multi-tenancy filtering.
 */
class WorkoutLog extends Model
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
        'exercise_id',
        'student_id',
        'performed_at',
        'set_number',
        'reps_completed',
        'load_used',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'performed_at' => 'date',
        'set_number' => 'integer',
        'reps_completed' => 'integer',
        'load_used' => 'decimal:2',
    ];

    /**
     * Get the workout that owns this log.
     */
    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    /**
     * Get the exercise that was performed.
     */
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    /**
     * Get the student who performed this exercise.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

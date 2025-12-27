<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Goal Model
 *
 * Represents a fitness goal for a student.
 * Uses BelongsToTenant trait for automatic multi-tenancy filtering.
 */
class Goal extends Model
{
    use HasFactory, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'student_id',
        'title',
        'description',
        'type',
        'target_value',
        'current_value',
        'unit',
        'starts_at',
        'target_date',
        'completed_at',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'starts_at' => 'date',
        'target_date' => 'date',
        'completed_at' => 'date',
    ];

    /**
     * Get the student that owns this goal.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

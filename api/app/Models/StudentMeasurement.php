<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * StudentMeasurement Model
 *
 * Represents physical measurements/assessments of a student.
 * Uses BelongsToTenant trait for automatic multi-tenancy filtering.
 */
class StudentMeasurement extends Model
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
        'weight',
        'body_fat',
        'muscle_mass',
        'chest',
        'waist',
        'hips',
        'right_arm',
        'left_arm',
        'right_thigh',
        'left_thigh',
        'right_calf',
        'left_calf',
        'notes',
        'measured_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'weight' => 'decimal:2',
        'body_fat' => 'decimal:2',
        'muscle_mass' => 'decimal:2',
        'chest' => 'decimal:2',
        'waist' => 'decimal:2',
        'hips' => 'decimal:2',
        'right_arm' => 'decimal:2',
        'left_arm' => 'decimal:2',
        'right_thigh' => 'decimal:2',
        'left_thigh' => 'decimal:2',
        'right_calf' => 'decimal:2',
        'left_calf' => 'decimal:2',
        'measured_at' => 'date',
    ];

    /**
     * Get the student that owns this measurement.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

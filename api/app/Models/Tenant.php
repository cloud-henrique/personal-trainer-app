<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Tenant Model
 *
 * Represents a Personal Trainer (tenant) in the multi-tenant system.
 * Uses UUID as primary key and does NOT use BelongsToTenant trait (it's the root).
 */
class Tenant extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'primary_color',
        'logo_url',
        'cover_url',
        'email',
        'phone',
        'plan',
        'is_active',
        'trial_ends_at',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'data' => 'array',
    ];

    /**
     * Get all users that belong to this tenant.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all students that belong to this tenant.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}

<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Payment Model
 *
 * Represents a payment/invoice for a student.
 * Future implementation with Asaas payment gateway integration.
 * Uses BelongsToTenant trait for automatic multi-tenancy filtering.
 */
class Payment extends Model
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
        'amount',
        'due_date',
        'paid_at',
        'status',
        'payment_method',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'date',
    ];

    /**
     * Get the student that owns this payment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

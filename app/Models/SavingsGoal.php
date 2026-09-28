<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingsGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'current_amount',
        'target_date',
        'description',
        'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'target_date' => 'date',
    ];

    /**
     * User who owns this savings goal.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate the percentage of the goal completed.
     */
    public function getProgressPercentageAttribute(): float
    {
        if ((float) $this->target_amount <= 0) {
            return 0;
        }

        $progress = ((float) $this->current_amount / (float) $this->target_amount) * 100;

        return min(round($progress, 2), 100);
    }

    /**
     * Calculate the amount remaining to reach the goal.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(
            (float) $this->target_amount - (float) $this->current_amount,
            0
        );
    }

    /**
     * Determine whether the goal has been completed.
     */
    public function getIsCompletedAttribute(): bool
    {
        return (float) $this->current_amount >= (float) $this->target_amount;
    }
}
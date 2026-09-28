<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'amount',
        'start_date',
        'end_date',
        'alert_percentage',
        'is_recurring',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'alert_percentage' => 'integer',
        'is_recurring' => 'boolean',
    ];

    /**
     * User who owns this budget.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Category this budget applies to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'key',
        'value',
    ];

    /**
     * User who owns this setting.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a setting value for a user.
     */
    public static function getValue(int $userId, string $key, mixed $default = null): mixed
    {
        return static::where('user_id', $userId)
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    /**
     * Create or update a user setting.
     */
    public static function setValue(
        int $userId,
        string $key,
        mixed $value
    ): self {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'key' => $key,
            ],
            [
                'value' => $value,
            ]
        );
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'activity_field_id',
        'plan_id',
        'status',
        'price_paid',
        'discount_price',
        'started_at',
        'expires_at',
        'cancelled_at',
        'payment_reference',
        're_evaluation_credits',
    ];

    const STATUS_PENDING = 'pending';

    const STATUS_ACTIVE = 'active';

    const STATUS_EXPIRED = 'expired';

    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_ACTIVE,
        self::STATUS_EXPIRED,
        self::STATUS_CANCELLED,
    ];

    protected function casts(): array
    {
        return [
            'price_paid' => 'decimal:0',
            'discount_price' => 'decimal:2',
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
            'cancelled_at' => 'datetime',
            're_evaluation_credits' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function activityField(): BelongsTo
    {
        return $this->belongsTo(ActivityField::class);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->expires_at !== null
            && $this->expires_at->isFuture();
    }
}

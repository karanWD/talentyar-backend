<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    use \App\Traits\MediaTrait;

    public $fillable = [
        'user_id',
        'score',
        'pros_comment',
        'cons_comment',
        'suggestion',
        'summary',
        'status'
    ];

    const status_pending = 'pending';

    const status_approved = 'approved';

    const status_rejected = 'rejected';

    const STATUSES = [
        self::status_pending,
        self::status_approved,
        self::status_rejected,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(EvaluationItem::class);
    }
}

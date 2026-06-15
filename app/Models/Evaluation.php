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
        'status',
        'analyzer_status',
        'analyzer_attempts',
        'analyzer_failure_count',
        'analyzer_success_count',
        'analyzer_last_error',
        'analyzer_last_attempt_at',
        'analyzer_succeeded_at',
    ];

    protected $casts = [
        'analyzer_last_attempt_at' => 'datetime',
        'analyzer_succeeded_at' => 'datetime',
    ];

    const status_pending = 'pending';

    const status_approved = 'approved';

    const status_rejected = 'rejected';

    const STATUSES = [
        self::status_pending,
        self::status_approved,
        self::status_rejected,
    ];

    const ANALYZER_STATUS_PENDING = 'pending';
    const ANALYZER_STATUS_PROCESSING = 'processing';
    const ANALYZER_STATUS_RETRYING = 'retrying';
    const ANALYZER_STATUS_SUCCEEDED = 'succeeded';
    const ANALYZER_STATUS_FAILED = 'failed';
    const ANALYZER_STATUS_SKIPPED = 'skipped';

    const ANALYZER_STATUSES = [
        self::ANALYZER_STATUS_PENDING,
        self::ANALYZER_STATUS_PROCESSING,
        self::ANALYZER_STATUS_RETRYING,
        self::ANALYZER_STATUS_SUCCEEDED,
        self::ANALYZER_STATUS_FAILED,
        self::ANALYZER_STATUS_SKIPPED,
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

<?php

namespace App\Models;

use App\Enums\ChallengeActionType;
use App\Enums\ChallengeType;
use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Challenge extends Model
{
    use MediaTrait;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    public const TRAINING_VIDEO_COLLECTION = 'training_video';

    protected $attributes = [
        'type' => ChallengeType::Score->value,
    ];

    protected $fillable = [
        'activity_field_id',
        'title',
        'description',
        'about',
        'conditions',
        'rules',
        'prize',
        'type',
        'action_type',
        'action_target_count',
        'status',
        'active_duration_days',
        'activated_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'activity_field_id' => 'integer',
            'type' => ChallengeType::class,
            'action_type' => ChallengeActionType::class,
            'action_target_count' => 'integer',
            'active_duration_days' => 'integer',
            'activated_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function activityField(): BelongsTo
    {
        return $this->belongsTo(ActivityField::class);
    }

    public function trainingVideo(): MorphOne
    {
        return $this->morphOne(Media::class, 'entity')
            ->where('collection', self::TRAINING_VIDEO_COLLECTION);
    }

    public function participations(): HasMany
    {
        return $this->hasMany(ChallengeParticipation::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'challenge_participations')
            ->withTimestamps();
    }

    public function scopeCurrentlyActive(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->whereNotNull('activated_at')
            ->where('activated_at', '<=', now())
            ->where('expires_at', '>', now());
    }

    public function isCurrentlyActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->activated_at !== null
            && $this->activated_at->lte(now())
            && $this->expires_at !== null
            && $this->expires_at->gt(now());
    }

    public function isScoreType(): bool
    {
        return $this->type === ChallengeType::Score;
    }

    public function isActionType(): bool
    {
        return $this->type === ChallengeType::Action;
    }

    /**
     * Keep the activation timestamps consistent with status and duration.
     */
    public function synchronizeActivationWindow(bool $restart = false): void
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            $this->activated_at = null;
            $this->expires_at = null;

            return;
        }

        if ($restart || $this->activated_at === null) {
            $this->activated_at = now();
        }

        $this->expires_at = $this->activated_at
            ->copy()
            ->addDays($this->active_duration_days);
    }
}

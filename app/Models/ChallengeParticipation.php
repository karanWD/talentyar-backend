<?php

namespace App\Models;

use App\Traits\MediaTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ChallengeParticipation extends Model
{
    use MediaTrait;

    public const MEDIA_ENTITY_SLUG = 'challenge_participation';

    public const MEDIA_COLLECTION = 'submission';

    protected $fillable = [
        'challenge_id',
        'user_id',
        'score',
        'scored_by_admin_id',
        'scored_at',
    ];

    protected function casts(): array
    {
        return [
            'challenge_id' => 'integer',
            'user_id' => 'integer',
            'score' => 'integer',
            'scored_by_admin_id' => 'integer',
            'scored_at' => 'datetime',
        ];
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scoredByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'scored_by_admin_id');
    }

    public function submission(): MorphOne
    {
        return $this->morphOne(Media::class, 'entity')
            ->where('collection', self::MEDIA_COLLECTION);
    }
}

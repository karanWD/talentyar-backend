<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use \App\Traits\MediaTrait;

    const STATE_PUBLISHED = 1;

    const STATE_DRAFT = 2;

    const STATE_REJECTED = 3;

    const STATES = [
        self::STATE_PUBLISHED,
        self::STATE_DRAFT,
        self::STATE_REJECTED,
    ];

    const TYPE_VIDEO = 'video';

    const TYPE_GALLERY = 'gallery';

    const TYPES = [
        self::TYPE_VIDEO,
        self::TYPE_GALLERY,
    ];

    protected $attributes = [
        'type' => self::TYPE_VIDEO,
    ];

    protected $fillable = [
        'user_id',
        'activity_fields_id',
        'evaluation_id',
        'type',
        'state',
        'caption',
        'height',
        'width',
    ];

    protected function casts(): array
    {
        return [
            'activity_fields_id' => 'integer',
            'evaluation_id' => 'integer',
            'height' => 'integer',
            'width' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activityField(): BelongsTo
    {
        return $this->belongsTo(ActivityField::class, 'activity_fields_id');
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function postLikes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class)->where('type', PostLike::TYPE_LIKE);
    }

    public function dislikes(): HasMany
    {
        return $this->hasMany(PostLike::class)->where('type', PostLike::TYPE_DISLIKE);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function postViews(): HasMany
    {
        return $this->hasMany(PostView::class);
    }
}

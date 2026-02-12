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

    protected $fillable = [
        'user_id',
        'state',
        'caption',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}

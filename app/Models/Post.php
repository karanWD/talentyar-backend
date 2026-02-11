<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}

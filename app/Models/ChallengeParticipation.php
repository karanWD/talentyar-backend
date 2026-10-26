<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeParticipation extends Model
{
    protected $fillable = [
        'challenge_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'challenge_id' => 'integer',
            'user_id' => 'integer',
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
}

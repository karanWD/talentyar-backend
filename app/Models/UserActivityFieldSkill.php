<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivityFieldSkill extends Model
{
    protected $fillable = [
        'user_id',
        'activity_field_id',
        'answers',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'activity_field_id' => 'integer',
        'answers' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activityField(): BelongsTo
    {
        return $this->belongsTo(ActivityField::class);
    }
}

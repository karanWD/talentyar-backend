<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    protected $fillable = [
        'name',
        'activity_field_id',
    ];

    protected $casts = [
        'activity_field_id' => 'integer',
    ];

    public function activityField(): BelongsTo
    {
        return $this->belongsTo(ActivityField::class);
    }
}

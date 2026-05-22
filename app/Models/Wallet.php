<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'balance',
        'currency',
        'entity_id',
        'entity_type',
    ];

    public function entity(): MorphTo
    {
        return $this->morphTo('entity');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}

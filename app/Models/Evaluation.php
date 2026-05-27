<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    //
    const status_pending = 'pending';
    const status_approved = 'approved';
    const status_rejected = 'rejected';

    const STATUSES = [
        self::status_pending,
        self::status_approved,
        self::status_rejected,
    ];
}

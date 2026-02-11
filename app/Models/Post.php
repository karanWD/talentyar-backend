<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    const STATE_PUBLISHED = 1;
    const STATE_DRAFT = 2;
    const STATE_REJECTED = 3;

    const STATES = [
        self::STATE_PUBLISHED,
        self::STATE_DRAFT,
        self::STATE_REJECTED,
    ];


}

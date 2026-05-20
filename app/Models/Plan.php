<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plans';

    protected $fillable = [
        'name',
        'price',
        'discount_price',
        'duration',
        'type',
        'status',
        'description',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    
    const STATUS = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];



    const TYPE_BASIC = 'basic';
    const TYPE_PRO = 'pro';
    const TYPE_VIP = 'vip';

    const TYPE = [
        self::TYPE_BASIC => 'Basic',
        self::TYPE_PRO => 'Pro',
        self::TYPE_VIP => 'VIP',
    ];

}

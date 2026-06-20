<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';

    const TYPE = [
      self::TYPE_IN,
      self::TYPE_OUT,
    ];

    const STATUS = [
        self::STATUS_PENDING,
        self::STATUS_SUCCESS,
        self::STATUS_FAIL,
    ];

    const PAYMENT_WALLET = 'payment_wallet';
    const PAYMENT_SUBSCRIPTION = 'payment_subscription';
    const PAYMENT_SUBSCRIPTION_EXTENDED = 'payment_subscription_extended';
    const PAYMENT = [
        self::PAYMENT_WALLET,
        self::PAYMENT_SUBSCRIPTION,
        self::PAYMENT_SUBSCRIPTION_EXTENDED,
    ];

    protected $fillable = [
        'wallet_id',
        'payment',
        'type',
        'status',
        'amount',
        'currency',
        'description',
        'subscription_id',
        'plan_id',
        'meta_data',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isIn(): bool
    {
        return $this->type === self::TYPE_IN;
    }

    public function isOut(): bool
    {
        return $this->type === self::TYPE_OUT;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isFail(): bool
    {
        return $this->status === self::STATUS_FAIL;
    }
}

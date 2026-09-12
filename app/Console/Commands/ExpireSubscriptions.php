<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Mark active subscriptions past their expiration time as expired';

    public function handle(): int
    {
        $expiredCount = Subscription::query()
            ->where('status', Subscription::STATUS_ACTIVE)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update(['status' => Subscription::STATUS_EXPIRED]);

        $this->info("Marked {$expiredCount} subscription(s) as expired.");

        return self::SUCCESS;
    }
}

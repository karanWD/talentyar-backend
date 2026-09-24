<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('subscriptions:expire')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('storage:migrate-s3', [
    '--source' => 's3_arvan',
    '--target' => 's3_parspack',
    '--continue-on-error' => true,
])
    ->dailyAt((string) config('s3-sync.time'))
    ->timezone((string) config('s3-sync.timezone'))
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->sendOutputTo(storage_path('logs/s3-sync.log'));

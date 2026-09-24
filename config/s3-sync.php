<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Daily S3 Replication Schedule
    |--------------------------------------------------------------------------
    |
    | New objects are copied from Arvan to ParsPack at this local time. Existing
    | destination objects are left untouched, making repeated runs safe.
    |
    */

    'time' => env('S3_SYNC_TIME', '02:00'),

    'timezone' => env('S3_SYNC_TIMEZONE', 'UTC'),

];

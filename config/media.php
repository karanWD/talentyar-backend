<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Max upload size (bytes)
    |--------------------------------------------------------------------------
    |
    | Used for media upload validation. Set high enough for large videos
    | (e.g. 400MB = 419430400). Your php.ini must allow it:
    |   upload_max_filesize = 512M
    |   post_max_size = 512M
    |   max_execution_time = 300
    | Nginx: client_max_body_size 512M;
    |
    */

    'max_upload_bytes' => (int) env('MEDIA_MAX_UPLOAD_MB', 100) * 1024 * 1024,

];

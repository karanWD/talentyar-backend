<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3_parspack' => [
            'driver' => 's3',
            'key' => env('PARSPACK_AWS_ACCESS_KEY_ID','EoA2STad1YXib3P2'),
            'secret' => env('PARSPACK_AWS_SECRET_ACCESS_KEY','EFSwLR8aWmESwDedP6XMjUkt7bVjCFwY'),
            'region' => env('PARSPACK_AWS_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('PARSPACK_AWS_BUCKET','c190045'),
            'url' => env('PARSPACK_AWS_URL','https://c190045.parspack.net'),
            'endpoint' => env('PARSPACK_AWS_ENDPOINT','https://c190045.parspack.net'),
            'use_path_style_endpoint' => env('PARSPACK_AWS_USE_PATH_STYLE_ENDPOINT', true),
            'visibility' => 'public',
            'throw' => true,
            'report' => false,
        ],

        's3_arvan' => [
            'driver' => 's3',
            'key' => env('ARVAN_AWS_ACCESS_KEY_ID', env('AWS_ACCESS_KEY_ID')),
            'secret' => env('ARVAN_AWS_SECRET_ACCESS_KEY', env('AWS_SECRET_ACCESS_KEY')),
            'region' => env('ARVAN_AWS_DEFAULT_REGION', env('AWS_DEFAULT_REGION', 'us-east-1')),
            'bucket' => env('ARVAN_AWS_BUCKET', env('AWS_BUCKET')),
            'url' => env('ARVAN_AWS_URL', env('AWS_URL')),
            'endpoint' => env('ARVAN_AWS_ENDPOINT', env('AWS_ENDPOINT')),
            'use_path_style_endpoint' => env('ARVAN_AWS_USE_PATH_STYLE_ENDPOINT', env('AWS_USE_PATH_STYLE_ENDPOINT', true)),
            'visibility' => 'public',
            'throw' => true,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];

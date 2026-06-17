<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'analyzer' => [
        'url' => env('ANALYZER_URL', 'http://109.122.249.234:3000/api/v1/analyze'),
    ],

    'evaluation_service' => [
        'token' => env('EVALUATION_SERVICE_TOKEN', 'kbyaagzNTcnaV-BYLBYTFHKPuYKqBH4BLJnhWy-jNnI'),
    ],

    'bitpay' => [
        'api' => env('BITPAY_API','efb05-a9246-fc73b-13faa-e9f11f5c20b4b17aab1e4cfc1da4'),
        'gateway_send_url' => env('BITPAY_GATEWAY_SEND_URL', 'https://bitpay.ir/payment/gateway-send'),
        'gateway_result_url' => env('BITPAY_GATEWAY_RESULT_URL', 'https://bitpay.ir/payment/gateway-result-second'),
        'gateway_url' => env('BITPAY_GATEWAY_URL', 'https://bitpay.ir/payment/gateway-%s-get'),
        'timeout' => (int) env('BITPAY_TIMEOUT', 15),
        'verify_ssl' => (bool) env('BITPAY_VERIFY_SSL', true),
    ],

];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services - RT/RW Net Management
    |--------------------------------------------------------------------------
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'telegram' => [
        'bot_token'    => env('TELEGRAM_BOT_TOKEN'),
        'bot_username' => env('TELEGRAM_BOT_USERNAME'),
    ],

    'ipaymu' => [
        'va'      => env('IPAYMU_VA'),
        'api_key' => env('IPAYMU_API_KEY'),
        'sandbox' => env('IPAYMU_SANDBOX', true),
    ],

    'midtrans' => [
        'server_key'  => env('MIDTRANS_SERVER_KEY'),
        'client_key'  => env('MIDTRANS_CLIENT_KEY'),
        'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
        'sandbox'     => env('MIDTRANS_SANDBOX', true),
    ],

    'duitku' => [
        'merchant_code' => env('DUITKU_MERCHANT_CODE'),
        'api_key'       => env('DUITKU_API_KEY'),
        'sandbox'       => env('DUITKU_SANDBOX', true),
    ],
];

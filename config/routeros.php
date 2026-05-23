<?php

declare(strict_types=1);

/**
 * RouterOS / MikroTik API Configuration
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Default Connection Settings
    |--------------------------------------------------------------------------
    */
    'default_timeout'  => env('MIKROTIK_TIMEOUT', 10),   // seconds
    'default_attempts' => env('MIKROTIK_ATTEMPTS', 3),
    'default_port'     => env('MIKROTIK_API_PORT', 8728),
    'default_ssl_port' => env('MIKROTIK_API_SSL_PORT', 8729),

    /*
    |--------------------------------------------------------------------------
    | Legacy Protocol (pre-6.43)
    |--------------------------------------------------------------------------
    | Set to true for RouterOS versions older than 6.43.
    | Modern routers (ROS 6.43+) use the new API protocol.
    */
    'legacy_protocol' => env('MIKROTIK_LEGACY', false),
];

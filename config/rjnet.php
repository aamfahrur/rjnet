<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | RT/RW Net Management Configuration
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Billing Settings
    |--------------------------------------------------------------------------
    */
    'billing' => [
        'grace_period_days'    => env('BILLING_GRACE_PERIOD_DAYS', 7),
        'due_date_offset_days' => env('BILLING_DUE_OFFSET_DAYS', 10),
        'reminder_days'        => [7, 3, 1],
        'auto_suspend'         => env('BILLING_AUTO_SUSPEND', true),
        'auto_unsuspend'       => env('BILLING_AUTO_UNSUSPEND', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Mikrotik Settings
    |--------------------------------------------------------------------------
    */
    'mikrotik' => [
        'default_api_port'         => env('MIKROTIK_API_PORT', 8728),
        'default_api_ssl_port'     => env('MIKROTIK_API_SSL_PORT', 8729),
        'connection_timeout'       => env('MIKROTIK_TIMEOUT', 10),
        'max_retries'              => env('MIKROTIK_MAX_RETRIES', 3),
        'metrics_interval_minutes' => env('MIKROTIK_METRICS_INTERVAL', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Settings
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'enabled'                => env('MONITORING_ENABLED', true),
        'collect_interval'       => env('MONITORING_INTERVAL', 5),
        'traffic_retention_days' => env('MONITORING_RETENTION_DAYS', 30),
        'alert_cpu_threshold'    => env('ALERT_CPU_THRESHOLD', 80),
        'alert_memory_threshold' => env('ALERT_MEMORY_THRESHOLD', 85),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'channels'            => ['system', 'email', 'telegram'],
        'telegram_bot_token'  => env('TELEGRAM_BOT_TOKEN'),
        'queue_notifications' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Ticket Settings
    |--------------------------------------------------------------------------
    */
    'ticket' => [
        'sla' => [
            'critical' => 2,   // hours
            'high'     => 8,
            'medium'   => 24,
            'low'      => 72,
        ],
        'auto_close_days'        => env('TICKET_AUTO_CLOSE_DAYS', 3),
        'max_attachment_size_mb' => env('TICKET_MAX_ATTACHMENT_MB', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        'rate_limit_api'     => env('API_RATE_LIMIT', 60),
        'max_login_attempts' => env('MAX_LOGIN_ATTEMPTS', 5),
        'session_lifetime'   => env('SESSION_LIFETIME', 120),
        'two_factor_enabled' => env('TWO_FACTOR_ENABLED', false),
    ],
];

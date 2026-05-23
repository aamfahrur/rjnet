<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    protected function cacheRemember(string $key, int $ttlSeconds, callable $callback): mixed
    {
        return Cache::remember($key, $ttlSeconds, $callback);
    }

    protected function cacheForget(string $key): void
    {
        Cache::forget($key);
    }

    protected function cacheFlush(string $prefix): void
    {
        // In production, use Redis pattern-based deletion
        Cache::flush();
    }

    protected function cacheKey(string ...$parts): string
    {
        return implode(':', $parts);
    }

    /**
     * Default TTL constants.
     */
    protected const TTL_SHORT = 60;        // 1 minute
    protected const TTL_MEDIUM = 300;       // 5 minutes
    protected const TTL_LONG = 3600;        // 1 hour
    protected const TTL_DAY = 86400;        // 24 hours
}

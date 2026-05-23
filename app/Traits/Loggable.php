<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait Loggable
{
    protected function logInfo(string $message, array $context = []): void
    {
        Log::info("[{$this->logPrefix()}] {$message}", $context);
    }

    protected function logError(string $message, array $context = []): void
    {
        Log::error("[{$this->logPrefix()}] {$message}", $context);
    }

    protected function logWarning(string $message, array $context = []): void
    {
        Log::warning("[{$this->logPrefix()}] {$message}", $context);
    }

    protected function logDebug(string $message, array $context = []): void
    {
        Log::debug("[{$this->logPrefix()}] {$message}", $context);
    }

    /**
     * Override in the using class to provide a log prefix.
     */
    protected function logPrefix(): string
    {
        return class_basename(static::class);
    }
}

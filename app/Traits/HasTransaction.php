<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Throwable;

trait HasTransaction
{
    /**
     * Execute a callback within a database transaction.
     */
    protected function transaction(callable $callback, int $attempts = 1): mixed
    {
        return DB::transaction($callback, $attempts);
    }

    /**
     * Begin a database transaction.
     */
    protected function beginTransaction(): void
    {
        DB::beginTransaction();
    }

    /**
     * Commit the current transaction.
     */
    protected function commit(): void
    {
        DB::commit();
    }

    /**
     * Rollback the current transaction.
     */
    protected function rollback(): void
    {
        DB::rollBack();
    }

    /**
     * Execute callback with transaction, automatic rollback on throwable.
     */
    protected function transactional(callable $callback): mixed
    {
        try {
            $this->beginTransaction();
            $result = $callback();
            $this->commit();
            return $result;
        } catch (Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }
}

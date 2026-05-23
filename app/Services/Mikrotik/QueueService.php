<?php

declare(strict_types=1);

namespace App\Services\Mikrotik;

use App\ValueObjects\NetworkSpeed;

/**
 * Queue Service - Manages Simple Queue and Queue Tree on Mikrotik RouterOS.
 */
class QueueService extends BaseMikrotikService
{
    // =========================================================================
    // Simple Queue Operations
    // =========================================================================

    /**
     * Get all simple queues.
     */
    public function getAllQueues(): array
    {
        return $this->read('/queue/simple/print');
    }

    /**
     * Get a simple queue by name.
     */
    public function getQueue(string $name): ?array
    {
        $results = $this->read('/queue/simple/print', ['name' => $name]);
        return $results[0] ?? null;
    }

    /**
     * Add a new simple queue for bandwidth management.
     */
    public function addSimpleQueue(
        string $name,
        string $target,
        NetworkSpeed $maxDownload,
        NetworkSpeed $maxUpload,
        ?NetworkSpeed $burstDownload = null,
        ?NetworkSpeed $burstUpload = null,
        ?string $parent = null,
        int $priority = 8,
    ): bool {
        $data = [
            'name'      => $name,
            'target'    => $target,
            'max-limit' => $maxUpload->toMikrotikFormat() . '/' . $maxDownload->toMikrotikFormat(),
            'priority'  => $priority . '/' . $priority,
        ];

        if ($parent) {
            $data['parent'] = $parent;
        }

        if ($burstDownload && $burstUpload) {
            $data['burst-limit'] = $burstUpload->toMikrotikFormat() . '/' . $burstDownload->toMikrotikFormat();
            $data['burst-threshold'] = $maxUpload->toMikrotikFormat() . '/' . $maxDownload->toMikrotikFormat();
            $data['burst-time'] = '10s';
        }

        $this->executeRetry(function () use ($data) {
            $this->add('/queue/simple/add', $data);
        });

        $this->logInfo('Simple queue created', ['name' => $name, 'target' => $target]);
        return true;
    }

    /**
     * Update an existing simple queue.
     */
    public function updateSimpleQueue(string $name, array $params): bool
    {
        $this->set('/queue/simple/set', ['name' => $name], $params);
        $this->logInfo('Simple queue updated', ['name' => $name]);
        return true;
    }

    /**
     * Remove a simple queue.
     */
    public function removeSimpleQueue(string $name): bool
    {
        $this->remove('/queue/simple/remove', ['name' => $name]);
        $this->logInfo('Simple queue removed', ['name' => $name]);
        return true;
    }

    /**
     * Enable a queue.
     */
    public function enableQueue(string $name): bool
    {
        $this->set('/queue/simple/set', ['name' => $name], ['disabled' => 'no']);
        return true;
    }

    /**
     * Disable a queue.
     */
    public function disableQueue(string $name): bool
    {
        $this->set('/queue/simple/set', ['name' => $name], ['disabled' => 'yes']);
        return true;
    }

    // =========================================================================
    // Queue Tree Operations
    // =========================================================================

    /**
     * Get all queue trees.
     */
    public function getAllQueueTrees(): array
    {
        return $this->read('/queue/tree/print');
    }

    /**
     * Add a queue tree entry.
     */
    public function addQueueTree(string $name, string $parent, array $params): bool
    {
        $data = array_merge(['name' => $name, 'parent' => $parent], $params);
        $this->add('/queue/tree/add', $data);
        $this->logInfo('Queue tree created', ['name' => $name]);
        return true;
    }

    /**
     * Remove a queue tree entry.
     */
    public function removeQueueTree(string $name): bool
    {
        $this->remove('/queue/tree/remove', ['name' => $name]);
        return true;
    }

    // =========================================================================
    // Monitoring
    // =========================================================================

    /**
     * Get queue statistics for a specific queue.
     */
    public function getQueueStats(string $name): ?array
    {
        $results = $this->read('/queue/simple/print', [
            'name'  => $name,
            'stats' => '',
        ]);
        return $results[0] ?? null;
    }

    /**
     * Monitor queue traffic in real-time (single snapshot).
     */
    public function monitorQueue(string $name): array
    {
        $results = $this->read('/queue/simple/monitor', [
            'numbers' => $name,
            'once'    => '',
        ], true);
        return $results[0] ?? [];
    }
}

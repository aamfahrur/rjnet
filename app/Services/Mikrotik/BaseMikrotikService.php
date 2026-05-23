<?php

declare(strict_types=1);

namespace App\Services\Mikrotik;

use App\Models\Router;
use App\Models\RouterLog;
use App\Traits\Loggable;
use Illuminate\Support\Facades\Cache;

/**
 * Base Mikrotik API Service menggunakan RouterOS API.
 *
 * Service ini menyediakan koneksi dasar ke Mikrotik RouterOS.
 * Semua operasi Mikrotik harus melalui service ini.
 *
 * @see https://github.com/evilfreelancer/routeros-api-php
 */
abstract class BaseMikrotikService
{
    use Loggable;

    protected Router $router;
    protected ?\RouterOS\Client $client = null;
    protected float $requestStartTime;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    protected function logPrefix(): string
    {
        return 'Mikrotik:' . $this->router->name;
    }

    // =========================================================================
    // Connection Management
    // =========================================================================

    /**
     * Connect ke Mikrotik RouterOS API.
     */
    protected function connect(): \RouterOS\Client
    {
        if ($this->client) {
            return $this->client;
        }

        try {
            $config = new \RouterOS\Config([
                'host'           => $this->router->host,
                'user'           => $this->router->username,
                'pass'           => $this->router->password,
                'port'           => $this->router->api_port,
                'attempts'       => 3,
                'timeout'        => 10,
                'socket_timeout' => 10,
            ]);

            if ($this->router->use_ssl) {
                $config->set('port', $this->router->api_ssl_port);
                $config->set('ssl', true);
            }

            $this->client = new \RouterOS\Client($config);
            $this->logDebug('Connected to router');

            return $this->client;
        } catch (\Exception $e) {
            $this->logError('Connection failed', [
                'host'  => $this->router->host,
                'error' => $e->getMessage(),
            ]);
            throw new \App\Exceptions\MikrotikConnectionException(
                "Failed to connect to router {$this->router->name}: {$e->getMessage()}",
                previous: $e
            );
        }
    }

    /**
     * Execute a Mikrotik query with logging.
     */
    protected function query(string $path, array $params = [], string $method = 'query'): \RouterOS\Query
    {
        $query = new \RouterOS\Query($path);
        foreach ($params as $key => $value) {
            $query->equal($key, $value);
        }
        return $query;
    }

    /**
     * Read data from Mikrotik.
     */
    protected function read(string $path, array $where = [], bool $stream = false): array
    {
        $this->startTimer();
        try {
            $query = $this->query($path);
            foreach ($where as $key => $value) {
                $query->equal($key, $value);
            }

            $client = $this->connect();
            $response = $client->query($query)->read($stream);

            $results = [];
            foreach ($response as $item) {
                $results[] = $item;
            }

            $this->logCommand('read', $path, $where, $results);
            return $results;
        } catch (\Exception $e) {
            $this->logCommand('read', $path, $where, null, 'error', $e->getMessage());
            throw $e;
        }
    }

    /**
     * Write (add) data to Mikrotik.
     */
    protected function add(string $path, array $data): ?string
    {
        $this->startTimer();
        try {
            $query = $this->query($path);
            foreach ($data as $key => $value) {
                if ($value !== null && $value !== '') {
                    $query->equal($key, (string) $value);
                }
            }

            $client = $this->connect();
            $response = $client->query($query)->read();

            $resultId = null;
            if (count($response) > 0) {
                $resultId = $response[0]['ret'] ?? null;
            }

            $this->logCommand('add', $path, $data, ['ret' => $resultId]);
            return $resultId;
        } catch (\Exception $e) {
            $this->logCommand('add', $path, $data, null, 'error', $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update/Set data on Mikrotik.
     */
    protected function set(string $path, array $where, array $data): void
    {
        $this->startTimer();
        try {
            $query = $this->query($path);
            foreach ($where as $key => $value) {
                $query->equal($key, $value);
            }
            foreach ($data as $key => $value) {
                if ($value !== null) {
                    $query->equal($key, (string) $value);
                }
            }

            $client = $this->connect();
            $client->query($query)->read();

            $this->logCommand('set', $path, array_merge($where, $data));
        } catch (\Exception $e) {
            $this->logCommand('set', $path, array_merge($where, $data), null, 'error', $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove data from Mikrotik.
     */
    protected function remove(string $path, array $where): void
    {
        $this->startTimer();
        try {
            $query = $this->query($path);
            foreach ($where as $key => $value) {
                $query->equal($key, (string) $value);
            }

            $client = $this->connect();
            $client->query($query)->read();

            $this->logCommand('remove', $path, $where);
        } catch (\Exception $e) {
            $this->logCommand('remove', $path, $where, null, 'error', $e->getMessage());
            throw $e;
        }
    }

    /**
     * Execute custom command with retry support.
     */
    protected function executeRetry(callable $callback, int $maxRetries = 3): mixed
    {
        $attempts = 0;
        $lastException = null;

        while ($attempts < $maxRetries) {
            try {
                return $callback();
            } catch (\Exception $e) {
                $lastException = $e;
                $attempts++;
                $this->logWarning("Retry attempt {$attempts}/{$maxRetries}", [
                    'error' => $e->getMessage(),
                ]);
                if ($attempts < $maxRetries) {
                    usleep(500000 * $attempts); // Exponential backoff
                }
            }
        }

        throw $lastException ?? new \RuntimeException('Max retries exceeded');
    }

    // =========================================================================
    // System Information
    // =========================================================================

    public function getSystemIdentity(): array
    {
        $cacheKey = "mikrotik:{$this->router->id}:identity";
        return Cache::remember($cacheKey, 300, function () {
            $result = $this->read('/system/identity/print');
            return $result[0] ?? ['name' => 'Unknown'];
        });
    }

    public function getSystemResources(): array
    {
        $result = $this->read('/system/resource/print');
        return $result[0] ?? [];
    }

    public function getInterfaces(): array
    {
        return $this->read('/interface/print');
    }

    public function getInterfaceByName(string $name): ?array
    {
        $results = $this->read('/interface/print', ['name' => $name]);
        return $results[0] ?? null;
    }

    public function getInterfaceTraffic(string $name): array
    {
        $results = $this->read('/interface/monitor-traffic', [
            'interface' => $name,
            'once'      => '',
        ], true);
        return $results[0] ?? [];
    }

    public function ping(string $address, int $count = 3): array
    {
        try {
            $results = $this->read('/ping', [
                'address' => $address,
                'count'   => (string) $count,
            ]);
            return $results[0] ?? [];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // =========================================================================
    // Router Status
    // =========================================================================

    public function checkHealth(): bool
    {
        try {
            $resources = $this->getSystemResources();
            $cpuLoad = (float) ($resources['cpu-load'] ?? 0);
            $freeMemory = (int) ($resources['free-memory'] ?? 0);
            $totalMemory = (int) ($resources['total-memory'] ?? 1);

            $memoryUsage = round((($totalMemory - $freeMemory) / $totalMemory) * 100, 1);

            // Update router status
            $this->router->markOnline();

            // Store metrics
            $this->router->metrics()->create([
                'cpu_load'           => $cpuLoad,
                'memory_usage'       => $memoryUsage,
                'total_memory'       => $totalMemory,
                'free_memory'        => $freeMemory,
                'hdd_total'          => (int) ($resources['total-hdd-space'] ?? 0),
                'hdd_free'           => (int) ($resources['free-hdd-space'] ?? 0),
                'uptime_seconds'     => $this->parseUptime($resources['uptime'] ?? '0s'),
                'active_connections' => (int) ($resources['active-connections'] ?? 0),
                'recorded_at'        => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            $this->router->markOffline();
            $this->logError('Health check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    protected function startTimer(): void
    {
        $this->requestStartTime = microtime(true);
    }

    protected function getElapsedMs(): float
    {
        return round((microtime(true) - $this->requestStartTime) * 1000, 2);
    }

    protected function logCommand(
        string $action,
        string $path,
        ?array $request = null,
        ?array $response = null,
        string $status = 'success',
        ?string $error = null,
    ): void {
        RouterLog::logCommand(
            router: $this->router,
            action: $action,
            command: $path,
            request: $request,
            response: $response,
            status: $status,
            error: $error,
            executionTime: $this->getElapsedMs(),
        );
    }

    protected function parseUptime(string $uptime): float
    {
        $seconds = 0;
        if (preg_match('/(\d+)w/', $uptime, $m)) {
            $seconds += (int) $m[1] * 604800;
        }
        if (preg_match('/(\d+)d/', $uptime, $m)) {
            $seconds += (int) $m[1] * 86400;
        }
        if (preg_match('/(\d+)h/', $uptime, $m)) {
            $seconds += (int) $m[1] * 3600;
        }
        if (preg_match('/(\d+)m/', $uptime, $m)) {
            $seconds += (int) $m[1] * 60;
        }
        if (preg_match('/(\d+)s/', $uptime, $m)) {
            $seconds += (int) $m[1];
        }
        return (float) $seconds;
    }
}

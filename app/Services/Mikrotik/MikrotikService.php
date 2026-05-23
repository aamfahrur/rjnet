<?php

declare(strict_types=1);

namespace App\Services\Mikrotik;

use App\Enums\RouterStatus;
use App\Models\Router;
use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Exceptions\ClientException;
use RouterOS\Exceptions\ConnectException;
use RouterOS\Query;

/**
 * MikroTik RouterOS API Service
 *
 * Wraps evilfreelancer/routeros-api-php for Laravel integration.
 */
class MikrotikService
{
    /**
     * Create a configured API client for the given router.
     */
    public function createClient(Router $router): Client
    {
        $config = new Config([
            'host'     => $router->host,
            'port'     => $router->api_port ?: config('routeros.default_port', 8728),
            'user'     => $router->username,
            'pass'     => $router->password,
            'timeout'  => config('routeros.default_timeout', 10),
            'attempts' => config('routeros.default_attempts', 3),
            'ssl'      => $router->use_ssl ?? false,
            'legacy'   => config('routeros.legacy_protocol', false),
        ]);

        return new Client($config);
    }

    /**
     * Test connection to the router and update its status.
     */
    public function testConnection(Router $router): array
    {
        $start = microtime(true);

        try {
            $client = $this->createClient($router);
            $identity = $client->query('/system/identity/print')->read();

            $latency = round((microtime(true) - $start) * 1000, 1); // ms

            // Update router info
            $router->update([
                'status'            => RouterStatus::ONLINE,
                'router_os_version' => $identity[0]['version'] ?? null,
                'last_checked_at'   => now(),
            ]);

            return [
                'success'    => true,
                'identity'   => $identity[0]['name'] ?? 'MikroTik',
                'version'    => $identity[0]['version'] ?? 'unknown',
                'latency_ms' => $latency,
            ];
        } catch (ConnectException $e) {
            $router->update(['status' => RouterStatus::OFFLINE, 'last_checked_at' => now()]);
            return ['success' => false, 'error' => 'Koneksi gagal: ' . $e->getMessage()];
        } catch (ClientException $e) {
            $router->update(['status' => RouterStatus::ERROR, 'last_checked_at' => now()]);
            return ['success' => false, 'error' => 'Auth gagal: ' . $e->getMessage()];
        } catch (\Throwable $e) {
            $router->update(['status' => RouterStatus::ERROR, 'last_checked_at' => now()]);
            return ['success' => false, 'error' => 'Error: ' . $e->getMessage()];
        }
    }

    // =========================================================================
    // PPPoE / Hotspot User Management
    // =========================================================================

    /**
     * Get all PPPoE secrets from the router.
     */
    public function getPppoeSecrets(Router $router): array
    {
        return $this->query($router, '/ppp/secret/print');
    }

    /**
     * Add a new PPPoE user.
     */
    public function addPppoeUser(Router $router, array $data): array
    {
        return $this->query($router, '/ppp/secret/add', $data);
    }

    /**
     * Update an existing PPPoE user.
     */
    public function updatePppoeUser(Router $router, string $id, array $data): array
    {
        return $this->query($router, '/ppp/secret/set', [
            '.id' => $id,
            ...$data,
        ]);
    }

    /**
     * Remove a PPPoE user.
     */
    public function removePppoeUser(Router $router, string $id): array
    {
        return $this->query($router, '/ppp/secret/remove', ['.id' => $id]);
    }

    /**
     * Get PPPoE active connections.
     */
    public function getPppoeActive(Router $router): array
    {
        return $this->query($router, '/ppp/active/print');
    }

    /**
     * Disconnect a PPPoE user by ID.
     */
    public function disconnectPppoeUser(Router $router, string $id): array
    {
        return $this->query($router, '/ppp/active/remove', ['.id' => $id]);
    }

    // =========================================================================
    // System / Monitoring
    // =========================================================================

    /**
     * Get system resources (CPU, memory, uptime).
     */
    public function getSystemResources(Router $router): array
    {
        $cpu = $this->query($router, '/system/resource/print');
        $uptime = $this->query($router, '/system/resource/uptime');

        return [
            'cpu_load'     => $cpu[0]['cpu-load'] ?? '0',
            'free_memory'  => $this->formatBytes((int) ($cpu[0]['free-memory'] ?? 0)),
            'total_memory' => $this->formatBytes((int) ($cpu[0]['total-memory'] ?? 0)),
            'uptime'       => $cpu[0]['uptime'] ?? '0s',
            'version'      => $cpu[0]['version'] ?? 'unknown',
            'board_name'   => $cpu[0]['board-name'] ?? 'unknown',
        ];
    }

    /**
     * Get interface traffic statistics.
     */
    public function getInterfaces(Router $router): array
    {
        return $this->query($router, '/interface/print');
    }

    /**
     * Monitor interface traffic in real-time (single snapshot).
     */
    public function monitorInterface(Router $router, string $interfaceName = ''): array
    {
        $query = new Query('/interface/monitor-traffic');
        if ($interfaceName) {
            $query->equal('interface', $interfaceName);
        }
        $query->equal('once', '');

        $client = $this->createClient($router);
        return $client->query($query)->read();
    }

    /**
     * Get IP addresses configured on the router.
     */
    public function getIpAddresses(Router $router): array
    {
        return $this->query($router, '/ip/address/print');
    }

    /**
     * Get ARP table.
     */
    public function getArpTable(Router $router): array
    {
        return $this->query($router, '/ip/arp/print');
    }

    /**
     * Get DHCP server leases.
     */
    public function getDhcpLeases(Router $router): array
    {
        return $this->query($router, '/ip/dhcp-server/lease/print');
    }

    /**
     * Get firewall NAT rules.
     */
    public function getNatRules(Router $router): array
    {
        return $this->query($router, '/ip/firewall/nat/print');
    }

    /**
     * Get simple queues (bandwidth management).
     */
    public function getQueues(Router $router): array
    {
        return $this->query($router, '/queue/simple/print');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Execute a read query against a router.
     */
    public function query(Router $router, string $endpoint, array $params = []): array
    {
        $client = $this->createClient($router);
        $query = new Query($endpoint);

        foreach ($params as $key => $value) {
            $query->equal($key, (string) $value);
        }

        return $client->query($query)->read();
    }

    /**
     * Execute a write command (add/set/remove) on a router.
     */
    public function execute(Router $router, string $endpoint, array $params = []): array
    {
        $client = $this->createClient($router);
        $query = new Query($endpoint);

        foreach ($params as $key => $value) {
            $query->equal($key, (string) $value);
        }

        return $client->query($query)->read();
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 1) . ' GiB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MiB';
        }
        return number_format($bytes / 1024, 1) . ' KiB';
    }
}

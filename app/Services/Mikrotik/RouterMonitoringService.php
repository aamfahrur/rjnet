<?php

declare(strict_types=1);

namespace App\Services\Mikrotik;

use App\Models\Router;
use App\Models\RouterMetric;
use App\Models\TrafficLog;

/**
 * Router Monitoring Service - Monitors router health and performance metrics.
 */
class RouterMonitoringService extends BaseMikrotikService
{
    /**
     * Collect all metrics from the router.
     */
    public function collectMetrics(): RouterMetric
    {
        $resources = $this->getSystemResources();

        $metric = $this->router->metrics()->create([
            'cpu_load'           => (float) ($resources['cpu-load'] ?? 0),
            'memory_usage'       => $this->calculateMemoryUsage($resources),
            'total_memory'       => (int) ($resources['total-memory'] ?? 0),
            'free_memory'        => (int) ($resources['free-memory'] ?? 0),
            'hdd_total'          => (int) ($resources['total-hdd-space'] ?? 0),
            'hdd_free'           => (int) ($resources['free-hdd-space'] ?? 0),
            'uptime_seconds'     => $this->parseUptime($resources['uptime'] ?? '0s'),
            'active_connections' => (int) ($resources['active-connections'] ?? 0),
            'pppoe_sessions'     => $this->countPPPoESessions(),
            'hotspot_sessions'   => $this->countHotspotSessions(),
            'dhcp_leases'        => $this->countDHCPLeases(),
            'interface_stats'    => $this->collectInterfaceStats(),
            'recorded_at'        => now(),
        ]);

        $this->logDebug('Metrics collected', [
            'cpu'    => $metric->cpu_load,
            'memory' => $metric->memory_usage,
            'pppoe'  => $metric->pppoe_sessions,
        ]);

        return $metric;
    }

    /**
     * Collect traffic data for all interfaces.
     */
    public function collectTrafficLogs(): array
    {
        $interfaces = $this->getInterfaces();
        $logs = [];

        foreach ($interfaces as $iface) {
            $name = $iface['name'] ?? '';
            if (empty($name) || ($iface['disabled'] ?? 'false') === 'true') {
                continue;
            }

            $logs[] = TrafficLog::create([
                'router_id'      => $this->router->id,
                'interface_name' => $name,
                'rx_bytes'       => (int) ($iface['rx-byte'] ?? 0),
                'tx_bytes'       => (int) ($iface['tx-byte'] ?? 0),
                'rx_packets'     => (int) ($iface['rx-packet'] ?? 0),
                'tx_packets'     => (int) ($iface['tx-packet'] ?? 0),
                'recorded_at'    => now(),
            ]);
        }

        return $logs;
    }

    /**
     * Get active online sessions.
     */
    public function collectOnlineSessions(): array
    {
        $sessions = [];

        // PPPoE active sessions
        try {
            $pppoeActive = $this->read('/ppp/active/print');
            foreach ($pppoeActive as $session) {
                $sessions[] = [
                    'session_type'   => 'pppoe',
                    'username'       => $session['name'] ?? '',
                    'ip_address'     => $session['address'] ?? null,
                    'calling_id'     => $session['caller-id'] ?? null,
                    'uptime_seconds' => $this->parseUptime($session['uptime'] ?? '0s'),
                    'bytes_in'       => (int) ($session['bytes-in'] ?? 0),
                    'bytes_out'      => (int) ($session['bytes-out'] ?? 0),
                ];
            }
        } catch (\Exception $e) {
            $this->logWarning('Failed to collect PPPoE sessions', ['error' => $e->getMessage()]);
        }

        return $sessions;
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    private function calculateMemoryUsage(array $resources): float
    {
        $total = (int) ($resources['total-memory'] ?? 1);
        $free = (int) ($resources['free-memory'] ?? 0);
        return round((($total - $free) / max($total, 1)) * 100, 1);
    }

    private function countPPPoESessions(): int
    {
        try {
            $active = $this->read('/ppp/active/print');
            return count($active);
        } catch (\Exception) {
            return 0;
        }
    }

    private function countHotspotSessions(): int
    {
        try {
            $active = $this->read('/ip/hotspot/active/print');
            return count($active);
        } catch (\Exception) {
            return 0;
        }
    }

    private function countDHCPLeases(): int
    {
        try {
            $leases = $this->read('/ip/dhcp-server/lease/print', ['status' => 'bound']);
            return count($leases);
        } catch (\Exception) {
            return 0;
        }
    }

    private function collectInterfaceStats(): array
    {
        $stats = [];
        try {
            $interfaces = $this->getInterfaces();
            foreach ($interfaces as $iface) {
                $name = $iface['name'] ?? '';
                if (empty($name)) {
                    continue;
                }

                $stats[$name] = [
                    'type'        => $iface['type'] ?? 'unknown',
                    'mac_address' => $iface['mac-address'] ?? null,
                    'running'     => ($iface['running'] ?? 'false') === 'true',
                    'rx_byte'     => (int) ($iface['rx-byte'] ?? 0),
                    'tx_byte'     => (int) ($iface['tx-byte'] ?? 0),
                ];
            }
        } catch (\Exception) {
            // Silently fail; interface stats are best-effort
        }
        return $stats;
    }
}

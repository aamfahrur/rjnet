<?php

declare(strict_types=1);

namespace App\Services\Mikrotik;

use App\Models\Router;
use Psr\Log\LoggerInterface;

/**
 * Mikrotik Service Factory - Centralized service creation.
 */
class MikrotikServiceFactory
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Create PPPoE service for a router.
     */
    public function pppoe(Router $router): PPPoEService
    {
        return new PPPoEService($router);
    }

    /**
     * Create Hotspot service for a router.
     */
    public function hotspot(Router $router): HotspotService
    {
        return new HotspotService($router);
    }

    /**
     * Create Queue service for a router.
     */
    public function queue(Router $router): QueueService
    {
        return new QueueService($router);
    }

    /**
     * Create Monitoring service for a router.
     */
    public function monitoring(Router $router): RouterMonitoringService
    {
        return new RouterMonitoringService($router);
    }

    /**
     * Create all services for a router.
     */
    public function all(Router $router): array
    {
        return [
            'pppoe'      => $this->pppoe($router),
            'hotspot'    => $this->hotspot($router),
            'queue'      => $this->queue($router),
            'monitoring' => $this->monitoring($router),
        ];
    }
}

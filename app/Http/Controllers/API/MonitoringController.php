<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Services\Mikrotik\MikrotikServiceFactory;
use Illuminate\Http\JsonResponse;

class MonitoringController extends Controller
{
    public function __construct(
        private readonly MikrotikServiceFactory $mikrotikFactory,
    ) {
    }

    /**
     * Get real-time router metrics.
     */
    public function routerMetrics(Router $router): JsonResponse
    {
        $metric = $router->metrics()->latest('recorded_at')->first();

        return response()->json([
            'router' => [
                'id'     => $router->id,
                'name'   => $router->name,
                'status' => $router->status->value,
            ],
            'metric' => $metric,
        ]);
    }

    /**
     * Get online users from a router.
     */
    public function onlineUsers(Router $router): JsonResponse
    {
        try {
            $monitoring = $this->mikrotikFactory->monitoring($router);
            $sessions = $monitoring->collectOnlineSessions();

            return response()->json([
                'router'   => $router->name,
                'total'    => count($sessions),
                'sessions' => $sessions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get interface traffic for dashboard chart.
     */
    public function trafficHistory(Router $router): JsonResponse
    {
        $traffic = $router->trafficLogs()
            ->where('recorded_at', '>=', now()->subDay())
            ->orderBy('recorded_at')
            ->get()
            ->groupBy('interface_name');

        $series = $traffic->map(function ($logs, $iface) {
            return [
                'name' => $iface,
                'data' => $logs->map(fn ($l) => [
                    'x' => $l->recorded_at->toISOString(),
                    'y' => round(($l->rx_bytes + $l->tx_bytes) * 8 / 1_000_000, 2),
                ]),
            ];
        })->values();

        return response()->json(['series' => $series]);
    }
}

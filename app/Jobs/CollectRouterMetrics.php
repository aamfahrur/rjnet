<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Router;
use App\Services\Mikrotik\MikrotikServiceFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CollectRouterMetrics implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 30;
    public int $tries = 3;
    public int $backoff = 5;

    public function __construct(
        public readonly Router $router,
    ) {
    }

    public function handle(MikrotikServiceFactory $factory): void
    {
        $monitoring = $factory->monitoring($this->router);
        $metric = $monitoring->collectMetrics();

        // Broadcast realtime update
        \App\Events\RouterMetricsUpdated::dispatch($this->router, $metric);
    }
}

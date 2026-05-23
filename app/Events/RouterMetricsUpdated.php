<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Router;
use App\Models\RouterMetric;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RouterMetricsUpdated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Router $router,
        public readonly RouterMetric $metric,
    ) {
    }
}

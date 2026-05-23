<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Billing\BillingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMonthlyInvoices implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;
    public int $tries = 1;

    public function handle(BillingService $billingService): void
    {
        $billingService->generateMonthlyInvoices();
        $billingService->markOverdueInvoices();
    }
}

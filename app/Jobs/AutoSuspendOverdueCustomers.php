<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Services\Customer\CustomerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoSuspendOverdueCustomers implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    public function __construct(
        public readonly int $graceDays = 7,
    ) {
    }

    public function handle(CustomerService $customerService): void
    {
        Customer::query()
            ->where('status', CustomerStatus::ACTIVE->value)
            ->whereHas('invoices', function ($query) {
                $query->where('status', \App\Enums\InvoiceStatus::OVERDUE->value)
                    ->where('due_date', '<', now()->subDays($this->graceDays));
            })
            ->each(function (Customer $customer) use ($customerService) {
                try {
                    $customerService->suspendCustomer(
                        $customer,
                        "Auto-suspend: outstanding invoice past {$this->graceDays} days grace period"
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Auto-suspend failed', [
                        'customer' => $customer->customer_code,
                        'error'    => $e->getMessage(),
                    ]);
                }
            });
    }
}

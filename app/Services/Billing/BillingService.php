<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Enums\CustomerStatus;
use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Subscription;
use App\Traits\HasTransaction;
use App\Traits\Loggable;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BillingService
{
    use HasTransaction;
    use Loggable;

    protected function logPrefix(): string
    {
        return 'BillingService';
    }

    // =========================================================================
    // Invoice Generation
    // =========================================================================

    /**
     * Generate monthly invoices for all active customers.
     * This is the main cron job entry point.
     */
    public function generateMonthlyInvoices(?Carbon $billingDate = null): array
    {
        $billingDate = $billingDate ?? now();
        $stats = ['total' => 0, 'generated' => 0, 'skipped' => 0, 'errors' => 0];

        $subscriptions = Subscription::with(['customer', 'package'])
            ->whereIn('status', [CustomerStatus::ACTIVE->value, CustomerStatus::SUSPENDED->value])
            ->where('billing_date', $billingDate->day)
            ->get();

        $stats['total'] = $subscriptions->count();

        foreach ($subscriptions as $subscription) {
            try {
                $existingInvoice = $this->findExistingInvoice($subscription, $billingDate);
                if ($existingInvoice) {
                    $stats['skipped']++;
                    continue;
                }

                $this->generateInvoiceForSubscription($subscription, $billingDate);
                $stats['generated']++;
            } catch (\Exception $e) {
                $this->logError('Failed to generate invoice', [
                    'subscription_id' => $subscription->id,
                    'error'           => $e->getMessage(),
                ]);
                $stats['errors']++;
            }
        }

        $this->logInfo('Monthly invoice generation completed', $stats);
        return $stats;
    }

    /**
     * Generate a single invoice for a subscription.
     */
    public function generateInvoiceForSubscription(Subscription $subscription, ?Carbon $billingDate = null): Invoice
    {
        $billingDate = $billingDate ?? now();
        $customer = $subscription->customer;
        $package = $subscription->package;

        $periodStart = $billingDate->copy()->startOfMonth();
        $periodEnd = $billingDate->copy()->endOfMonth();
        $dueDate = $billingDate->copy()->addDays(10);

        $effectivePrice = $subscription->getEffectivePrice();

        return $this->transactional(function () use (
            $customer,
            $subscription,
            $package,
            $effectivePrice,
            $periodStart,
            $periodEnd,
            $dueDate,
            $billingDate
        ) {
            $invoice = Invoice::create([
                'customer_id'          => $customer->id,
                'subscription_id'      => $subscription->id,
                'subtotal'             => $effectivePrice,
                'tax_amount'           => 0,
                'discount_amount'      => 0,
                'total_amount'         => $effectivePrice,
                'paid_amount'          => 0,
                'remaining_amount'     => $effectivePrice,
                'status'               => InvoiceStatus::PENDING,
                'issue_date'           => $billingDate,
                'due_date'             => $dueDate,
                'billing_period_start' => $periodStart,
                'billing_period_end'   => $periodEnd,
            ]);

            // Invoice item
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => "Layanan Internet: {$package->name} ({$periodStart->format('d M')} - {$periodEnd->format('d M Y')})",
                'quantity'    => 1,
                'unit_price'  => $effectivePrice,
                'subtotal'    => $effectivePrice,
                'type'        => 'package',
            ]);

            $this->logInfo('Invoice generated', [
                'invoice'  => $invoice->invoice_number,
                'customer' => $customer->full_name,
                'amount'   => $effectivePrice,
            ]);

            return $invoice;
        });
    }

    // =========================================================================
    // Invoice Management
    // =========================================================================

    /**
     * Check and mark overdue invoices.
     */
    public function markOverdueInvoices(): int
    {
        $count = 0;

        Invoice::query()
            ->where('status', InvoiceStatus::PENDING->value)
            ->where('due_date', '<', now())
            ->chunk(100, function (Collection $invoices) use (&$count) {
                foreach ($invoices as $invoice) {
                    $invoice->markAsOverdue();
                    $count++;
                }
            });

        $this->logInfo('Marked overdue invoices', ['count' => $count]);
        return $count;
    }

    /**
     * Cancel an invoice.
     */
    public function cancelInvoice(Invoice $invoice, string $reason): bool
    {
        if (!$invoice->status->canBeCancelled()) {
            throw new \RuntimeException("Invoice {$invoice->invoice_number} cannot be cancelled. Current status: {$invoice->status->label()}");
        }

        $invoice->update([
            'status' => InvoiceStatus::CANCELLED,
            'notes'  => "Cancelled: {$reason}",
        ]);

        $this->logInfo('Invoice cancelled', [
            'invoice' => $invoice->invoice_number,
            'reason'  => $reason,
        ]);

        return true;
    }

    /**
     * Get customer's outstanding invoices.
     */
    public function getCustomerOutstandingInvoices(Customer $customer): Collection
    {
        return $customer->invoices()
            ->whereIn('status', [InvoiceStatus::PENDING->value, InvoiceStatus::OVERDUE->value])
            ->orderBy('due_date')
            ->get();
    }

    // =========================================================================
    // Reminder Checks
    // =========================================================================

    /**
     * Get invoices that need reminders (H-7, H-3, H-1).
     */
    public function getInvoicesForReminder(int $daysBeforeDue): Collection
    {
        $targetDate = now()->addDays($daysBeforeDue)->startOfDay();

        return Invoice::query()
            ->with('customer')
            ->where('status', InvoiceStatus::PENDING->value)
            ->whereDate('due_date', $targetDate)
            ->whereDoesntHave('reminders', function ($query) use ($daysBeforeDue) {
                $query->where('days_before_due', $daysBeforeDue);
            })
            ->get();
    }

    // =========================================================================
    // Auto Suspend/Unsuspend
    // =========================================================================

    /**
     * Auto-suspend customers with overdue invoices past grace period.
     */
    public function autoSuspendOverdueCustomers(int $graceDays = 7): int
    {
        $count = 0;
        $cutoffDate = now()->subDays($graceDays);

        Customer::query()
            ->where('status', CustomerStatus::ACTIVE->value)
            ->whereHas('invoices', function ($query) use ($cutoffDate) {
                $query->where('status', InvoiceStatus::OVERDUE->value)
                    ->where('due_date', '<', $cutoffDate);
            })
            ->chunk(100, function (Collection $customers) use (&$count) {
                foreach ($customers as $customer) {
                    if ($subscription = $customer->activeSubscription) {
                        $subscription->suspend();
                        $customer->update(['status' => CustomerStatus::SUSPENDED]);
                        $count++;
                        $this->logInfo('Auto-suspended customer', ['customer' => $customer->full_name]);
                    }
                }
            });

        return $count;
    }

    /**
     * Auto-unsuspend customers who have paid all outstanding invoices.
     */
    public function autoUnsuspendPaidCustomers(): int
    {
        $count = 0;

        Customer::query()
            ->where('status', CustomerStatus::SUSPENDED->value)
            ->whereDoesntHave('invoices', function ($query) {
                $query->whereIn('status', [
                    InvoiceStatus::PENDING->value,
                    InvoiceStatus::OVERDUE->value,
                ]);
            })
            ->chunk(100, function (Collection $customers) use (&$count) {
                foreach ($customers as $customer) {
                    if ($subscription = $customer->activeSubscription) {
                        $subscription->unsuspend();
                        $customer->update(['status' => CustomerStatus::ACTIVE]);
                        $count++;
                        $this->logInfo('Auto-unsuspended customer', ['customer' => $customer->full_name]);
                    }
                }
            });

        return $count;
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    private function findExistingInvoice(Subscription $subscription, Carbon $billingDate): ?Invoice
    {
        return Invoice::where('subscription_id', $subscription->id)
            ->where('billing_period_start', '<=', $billingDate->startOfMonth())
            ->where('billing_period_end', '>=', $billingDate->endOfMonth())
            ->whereNotIn('status', [InvoiceStatus::CANCELLED->value])
            ->first();
    }
}

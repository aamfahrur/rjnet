<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Notification;
use App\Services\Billing\BillingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBillingReminders implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 300;

    public function __construct(
        public readonly int $daysBeforeDue,
    ) {
    }

    public function handle(BillingService $billingService): void
    {
        $invoices = $billingService->getInvoicesForReminder($this->daysBeforeDue);

        foreach ($invoices as $invoice) {
            // Create reminder record
            \App\Models\BillingReminder::create([
                'invoice_id'      => $invoice->id,
                'customer_id'     => $invoice->customer_id,
                'days_before_due' => $this->daysBeforeDue,
                'channel'         => 'system',
                'status'          => 'pending',
            ]);

            // Create notification for customer
            Notification::create([
                'customer_id' => $invoice->customer_id,
                'type'        => 'invoice',
                'channel'     => 'system',
                'title'       => 'Pengingat Pembayaran',
                'message'     => "Tagihan #{$invoice->invoice_number} akan jatuh tempo dalam {$this->daysBeforeDue} hari. Jumlah: {$invoice->total_formatted}",
                'data'        => ['invoice_id' => $invoice->id],
                'target_url'  => route('customer.invoices.show', $invoice),
            ]);

            // Dispatch Telegram notification if customer has Telegram
            if ($invoice->customer->user?->telegram_chat_id) {
                SendTelegramNotification::dispatch(
                    $invoice->customer->user->telegram_chat_id,
                    "🔔 *Pengingat Pembayaran*\n\n"
                    . "Tagihan #{$invoice->invoice_number}\n"
                    . "Jatuh tempo: {$invoice->due_date->format('d M Y')}\n"
                    . "Jumlah: {$invoice->total_formatted}\n\n"
                    . 'Segera lakukan pembayaran sebelum layanan di-suspend.'
                );
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentGatewayConfig;
use App\Models\PaymentLog;
use App\Traits\HasTransaction;
use App\Traits\Loggable;
use Illuminate\Support\Collection;

/**
 * Payment Service - Main payment orchestrator.
 * Uses Strategy pattern to delegate to specific gateway drivers.
 */
class PaymentService
{
    use HasTransaction;
    use Loggable;

    protected function logPrefix(): string
    {
        return 'PaymentService';
    }

    // =========================================================================
    // Payment Creation
    // =========================================================================

    /**
     * Create a payment and initiate transaction with the selected gateway.
     */
    public function createPayment(
        Invoice $invoice,
        PaymentGatewayConfig $gatewayConfig,
        array $params = [],
    ): Payment {
        $customer = $invoice->customer;
        $adminFee = $gatewayConfig->calculateAdminFee($invoice->remaining_amount);
        $totalAmount = $invoice->remaining_amount + $adminFee;

        return $this->transactional(function () use (
            $invoice,
            $customer,
            $gatewayConfig,
            $adminFee,
            $totalAmount,
            $params
        ) {
            // Create payment record
            $payment = Payment::create([
                'invoice_id'         => $invoice->id,
                'customer_id'        => $customer->id,
                'payment_gateway_id' => $gatewayConfig->id,
                'gateway'            => $gatewayConfig->code,
                'method'             => $params['method'] ?? 'va',
                'channel'            => $params['channel'] ?? 'bca',
                'amount'             => $invoice->remaining_amount,
                'admin_fee'          => $adminFee,
                'total_amount'       => $totalAmount,
                'status'             => 'pending',
                'expired_at'         => now()->addHours(24),
            ]);

            // Initiate transaction with driver
            $driver = $this->resolveDriver($gatewayConfig);
            $result = $driver->createTransaction($payment, $invoice, $params);

            if ($result['success'] ?? false) {
                $payment->update([
                    'gateway_transaction_id' => $result['transaction_id'],
                    'payment_url'            => $result['payment_url'] ?? null,
                    'va_number'              => $result['va_number'] ?? null,
                    'qr_url'                 => $result['qr_url'] ?? null,
                    'gateway_request'        => $params,
                    'gateway_response'       => $result,
                ]);

                PaymentLog::log(
                    gateway: $gatewayConfig->code,
                    event: 'create_transaction',
                    request: $params,
                    response: $result,
                    paymentId: $payment->id,
                );
            } else {
                $payment->markAsFailed($result['error'] ?? 'Failed to create transaction');

                PaymentLog::log(
                    gateway: $gatewayConfig->code,
                    event: 'create_transaction_failed',
                    request: $params,
                    response: $result,
                    status: 'failed',
                    error: $result['error'] ?? null,
                    paymentId: $payment->id,
                );

                throw new \RuntimeException(
                    'Payment gateway error: ' . ($result['error'] ?? 'Unknown error')
                );
            }

            $this->logInfo('Payment created', [
                'payment' => $payment->payment_number,
                'gateway' => $gatewayConfig->code,
                'amount'  => $totalAmount,
            ]);

            return $payment;
        });
    }

    // =========================================================================
    // Callback Processing
    // =========================================================================

    /**
     * Process payment callback from any gateway.
     */
    public function processCallback(string $gatewayCode, array $payload): array
    {
        $gatewayConfig = PaymentGatewayConfig::where('code', $gatewayCode)->firstOrFail();
        $driver = $this->resolveDriver($gatewayConfig);

        // Validate callback
        if (!$driver->validateCallback($payload)) {
            PaymentLog::log($gatewayCode, 'callback_invalid', $payload, null, 'failed', 'Invalid signature');
            return ['success' => false, 'error' => 'Invalid callback signature'];
        }

        // Parse callback
        $callbackData = $driver->handleCallback($payload);

        // Find payment
        $payment = Payment::where('payment_number', $callbackData['reference_id'] ?? '')->first();

        if (!$payment) {
            PaymentLog::log($gatewayCode, 'callback_not_found', $payload, null, 'failed', 'Payment not found');
            return ['success' => false, 'error' => 'Payment not found'];
        }

        if ($callbackData['status'] === 'success') {
            $this->confirmPayment($payment, $callbackData, $gatewayCode);
            return ['success' => true, 'payment' => $payment];
        }

        PaymentLog::log($gatewayCode, 'callback_processed', $payload, $callbackData, 'pending', null, $payment->id);
        return ['success' => true, 'status' => 'pending'];
    }

    /**
     * Confirm a successful payment.
     */
    public function confirmPayment(Payment $payment, array $callbackData, string $gatewayCode): void
    {
        $this->transactional(function () use ($payment, $callbackData, $gatewayCode) {
            // Update payment
            $payment->markAsSuccess($callbackData, $callbackData['paid_by'] ?? 'unknown');

            // Update invoice
            $invoice = $payment->invoice;
            $newPaid = $invoice->paid_amount + $payment->amount;
            $newRemaining = $invoice->total_amount - $newPaid;

            $invoiceStatus = $newRemaining <= 0
                ? \App\Enums\InvoiceStatus::PAID
                : \App\Enums\InvoiceStatus::PARTIALLY_PAID;

            $invoice->update([
                'paid_amount'      => $newPaid,
                'remaining_amount' => max(0, $newRemaining),
                'status'           => $invoiceStatus,
                'paid_at'          => $newRemaining <= 0 ? now() : $invoice->paid_at,
            ]);

            // Auto unsuspend customer if applicable
            if ($invoiceStatus === \App\Enums\InvoiceStatus::PAID) {
                $customer = $invoice->customer;
                if ($customer->isSuspended()) {
                    $customer->activeSubscription?->unsuspend();
                    $customer->update(['status' => \App\Enums\CustomerStatus::ACTIVE]);
                }
            }

            PaymentLog::log($gatewayCode, 'payment_confirmed', $callbackData, ['status' => 'success'], 'success', null, $payment->id);

            $this->logInfo('Payment confirmed', [
                'payment' => $payment->payment_number,
                'invoice' => $invoice->invoice_number,
                'amount'  => $payment->amount,
            ]);
        });
    }

    // =========================================================================
    // Payment Status
    // =========================================================================

    /**
     * Check payment status with gateway.
     */
    public function checkPaymentStatus(Payment $payment): array
    {
        if (!$payment->gateway) {
            return ['status' => $payment->status];
        }

        $gatewayConfig = PaymentGatewayConfig::where('code', $payment->gateway)->first();
        if (!$gatewayConfig) {
            return ['status' => $payment->status];
        }

        $driver = $this->resolveDriver($gatewayConfig);
        return $driver->checkTransaction($payment->gateway_transaction_id);
    }

    // =========================================================================
    // Gateway Methods
    // =========================================================================

    /**
     * Get available payment methods from a gateway.
     */
    public function getAvailableMethods(PaymentGatewayConfig $gatewayConfig): array
    {
        $driver = $this->resolveDriver($gatewayConfig);
        return $driver->getPaymentMethods();
    }

    /**
     * Get all active gateways with their methods.
     */
    public function getActiveGateways(): Collection
    {
        return PaymentGatewayConfig::active()
            ->orderBy('sort_order')
            ->get()
            ->map(function ($gateway) {
                $driver = $this->resolveDriver($gateway);
                return [
                    'id'                   => $gateway->id,
                    'code'                 => $gateway->code,
                    'name'                 => $gateway->name,
                    'logo'                 => $gateway->logo,
                    'admin_fee_percentage' => $gateway->admin_fee_percentage,
                    'admin_fee_fixed'      => $gateway->admin_fee_fixed,
                    'methods'              => $driver->getPaymentMethods(),
                ];
            });
    }

    // =========================================================================
    // Driver Resolution
    // =========================================================================

    /**
     * Resolve the appropriate driver for a gateway configuration.
     */
    private function resolveDriver(PaymentGatewayConfig $gatewayConfig): PaymentGatewayInterface
    {
        $driverClass = $gatewayConfig->driver_class;

        if (!class_exists($driverClass)) {
            throw new \RuntimeException("Driver class {$driverClass} not found");
        }

        return new $driverClass($gatewayConfig->config);
    }
}

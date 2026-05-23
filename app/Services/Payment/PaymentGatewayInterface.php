<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Invoice;
use App\Models\Payment;

/**
 * Payment Gateway Interface.
 * All payment gateway drivers must implement this interface.
 * Uses the Strategy pattern for multi-gateway support.
 */
interface PaymentGatewayInterface
{
    /**
     * Create a payment transaction.
     */
    public function createTransaction(Payment $payment, Invoice $invoice, array $params = []): array;

    /**
     * Check transaction status from gateway.
     */
    public function checkTransaction(string $gatewayTransactionId): array;

    /**
     * Process callback/webhook from payment gateway.
     */
    public function handleCallback(array $payload): array;

    /**
     * Validate callback/webhook signature.
     */
    public function validateCallback(array $payload): bool;

    /**
     * Get available payment methods/channels.
     */
    public function getPaymentMethods(): array;

    /**
     * Get gateway name.
     */
    public function getGatewayName(): string;

    /**
     * Calculate admin fee for the given amount.
     */
    public function calculateAdminFee(int $amount): int;

    /**
     * Check if gateway is in sandbox mode.
     */
    public function isSandbox(): bool;
}

<?php

declare(strict_types=1);

namespace App\Services\Payment\Drivers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Payment\PaymentGatewayInterface;
use App\Traits\Loggable;
use Illuminate\Support\Facades\Http;

/**
 * Duitku Payment Gateway Driver.
 *
 * @see https://docs.duitku.com/api
 */
class DuitkuDriver implements PaymentGatewayInterface
{
    use Loggable;

    private array $config;
    private string $baseUrl;
    private string $merchantCode;
    private string $apiKey;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->baseUrl = $config['sandbox'] ?? true
            ? 'https://sandbox.duitku.com/webapi/api'
            : 'https://passport.duitku.com/webapi/api';
        $this->merchantCode = $config['merchant_code'] ?? '';
        $this->apiKey = $config['api_key'] ?? '';
    }

    protected function logPrefix(): string
    {
        return 'Duitku';
    }

    public function getGatewayName(): string
    {
        return 'Duitku';
    }

    public function isSandbox(): bool
    {
        return $this->config['sandbox'] ?? true;
    }

    public function createTransaction(Payment $payment, Invoice $invoice, array $params = []): array
    {
        $customer = $invoice->customer;

        $body = [
            'merchantCode'    => $this->merchantCode,
            'paymentAmount'   => $payment->total_amount,
            'merchantOrderId' => $payment->payment_number,
            'productDetails'  => "Pembayaran Invoice {$invoice->invoice_number}",
            'email'           => $customer->email ?? '',
            'phoneNumber'     => $customer->phone,
            'customerVaName'  => $customer->full_name,
            'paymentMethod'   => $params['method'] ?? 'DA',
            'returnUrl'       => $params['return_url'] ?? route('customer.invoices.show', $invoice),
            'callbackUrl'     => $params['notify_url'] ?? route('api.payment.callback', ['gateway' => 'duitku']),
            'expiryPeriod'    => 1440, // 24 hours in minutes
        ];

        $timestamp = now()->timestamp * 1000;
        $signature = md5($this->merchantCode . $timestamp . $this->apiKey);

        try {
            $response = Http::withHeaders([
                'Content-Type'       => 'application/json',
                'x-duitku-signature' => $signature,
                'x-duitku-timestamp' => $timestamp,
            ])->post("{$this->baseUrl}/merchant/v2/inquiry", $body);

            $result = $response->json();

            if ($response->successful() && ($result['statusCode'] ?? '') === '00') {
                return [
                    'success'        => true,
                    'transaction_id' => $result['reference'] ?? null,
                    'payment_url'    => $result['paymentUrl'] ?? null,
                    'va_number'      => $result['vaNumber'] ?? null,
                    'raw'            => $result,
                ];
            }

            return [
                'success' => false,
                'error'   => $result['statusMessage'] ?? 'Unknown error',
                'raw'     => $result,
            ];
        } catch (\Exception $e) {
            $this->logError('Transaction failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function checkTransaction(string $gatewayTransactionId): array
    {
        $timestamp = now()->timestamp * 1000;
        $signature = md5($this->merchantCode . $timestamp . $this->apiKey);

        $response = Http::withHeaders([
            'x-duitku-signature' => $signature,
            'x-duitku-timestamp' => $timestamp,
        ])->get("{$this->baseUrl}/merchant/transactionStatus", [
            'merchantCode'    => $this->merchantCode,
            'merchantOrderId' => $gatewayTransactionId,
        ]);

        return $response->json();
    }

    public function handleCallback(array $payload): array
    {
        return [
            'transaction_id' => $payload['reference'] ?? null,
            'reference_id'   => $payload['merchantOrderId'] ?? null,
            'status'         => ($payload['resultCode'] ?? '') === '00' ? 'success' : 'failed',
            'paid_by'        => $payload['paymentMethod'] ?? 'unknown',
            'paid_amount'    => (int) ($payload['amount'] ?? 0),
            'raw'            => $payload,
        ];
    }

    public function validateCallback(array $payload): bool
    {
        $signature = md5(
            ($payload['amount'] ?? '')
            . ($payload['merchantOrderId'] ?? '')
            . ($payload['resultCode'] ?? '')
            . ($payload['reference'] ?? '')
            . $this->apiKey
        );
        return $signature === ($payload['signature'] ?? '');
    }

    public function getPaymentMethods(): array
    {
        return [
            'VA' => ['label' => 'Virtual Account', 'channels' => []],
            'DA' => ['label' => 'Doku Wallet', 'channels' => []],
            'OV' => ['label' => 'OVO', 'channels' => []],
            'SA' => ['label' => 'Shopee Pay', 'channels' => []],
        ];
    }

    public function calculateAdminFee(int $amount): int
    {
        return 2000; // Flat fee
    }
}

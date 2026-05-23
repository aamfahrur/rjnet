<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Invoice;
use App\Models\Payment;
use App\Traits\Loggable;
use Illuminate\Support\Facades\Http;

/**
 * iPaymu Payment Gateway Driver.
 *
 * @see https://ipaymu.com/api
 */
class IPaymuDriver implements PaymentGatewayInterface
{
    use Loggable;

    private array $config;
    private string $baseUrl;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->baseUrl = $config['sandbox'] ?? true
            ? 'https://sandbox.ipaymu.com/api/v2'
            : 'https://my.ipaymu.com/api/v2';
    }

    protected function logPrefix(): string
    {
        return 'IPaymu';
    }

    public function getGatewayName(): string
    {
        return 'iPaymu';
    }

    public function isSandbox(): bool
    {
        return $this->config['sandbox'] ?? true;
    }

    // =========================================================================
    // Transaction
    // =========================================================================

    public function createTransaction(Payment $payment, Invoice $invoice, array $params = []): array
    {
        $customer = $invoice->customer;
        $method = $params['method'] ?? 'va';
        $channel = $params['channel'] ?? 'bca';

        $body = [
            'product'        => [$invoice->invoice_number],
            'qty'            => [1],
            'price'          => [$invoice->total_amount],
            'amount'         => $payment->total_amount,
            'returnUrl'      => $params['return_url'] ?? route('customer.invoices.show', $invoice),
            'cancelUrl'      => $params['cancel_url'] ?? route('customer.invoices.show', $invoice),
            'notifyUrl'      => $params['notify_url'] ?? route('api.payment.callback', ['gateway' => 'ipaymu']),
            'referenceId'    => $payment->payment_number,
            'buyerName'      => $customer->full_name,
            'buyerEmail'     => $customer->email ?? '',
            'buyerPhone'     => $customer->phone,
            'paymentMethod'  => $method,
            'paymentChannel' => $channel,
            'expired'        => 24, // 24 hours
        ];

        $headers = $this->generateHeaders($body);

        try {
            $response = Http::withHeaders($headers)
                ->post("{$this->baseUrl}/payment", $body);

            $result = $response->json();

            if ($response->successful() && ($result['Status'] ?? 0) === 200) {
                $this->logInfo('Transaction created', [
                    'payment'     => $payment->payment_number,
                    'gateway_trx' => $result['Data']['TransactionId'] ?? null,
                ]);

                return [
                    'success'        => true,
                    'transaction_id' => $result['Data']['TransactionId'] ?? null,
                    'payment_url'    => $result['Data']['PaymentUrl'] ?? null,
                    'va_number'      => $result['Data']['PaymentNo'] ?? null,
                    'qr_url'         => $result['Data']['QrCode'] ?? null,
                    'raw'            => $result,
                ];
            }

            $this->logError('Transaction failed', ['response' => $result]);
            return [
                'success' => false,
                'error'   => $result['Message'] ?? 'Unknown error',
                'raw'     => $result,
            ];
        } catch (\Exception $e) {
            $this->logError('Transaction request failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function checkTransaction(string $gatewayTransactionId): array
    {
        $body = ['transactionId' => $gatewayTransactionId];
        $headers = $this->generateHeaders($body);

        try {
            $response = Http::withHeaders($headers)
                ->post("{$this->baseUrl}/transaction", $body);

            return $response->json();
        } catch (\Exception $e) {
            $this->logError('Check transaction failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    // =========================================================================
    // Callback
    // =========================================================================

    public function handleCallback(array $payload): array
    {
        $status = $payload['Status'] ?? 0;
        $transactionId = $payload['TransactionId'] ?? null;
        $referenceId = $payload['ReferenceId'] ?? null;

        $this->logInfo('Callback received', [
            'status' => $status,
            'trx'    => $transactionId,
            'ref'    => $referenceId,
        ]);

        return [
            'transaction_id' => $transactionId,
            'reference_id'   => $referenceId,
            'status'         => $status === 200 ? 'success' : 'failed',
            'paid_by'        => $payload['PaymentChannel'] ?? 'unknown',
            'paid_amount'    => (int) ($payload['Amount'] ?? 0),
            'raw'            => $payload,
        ];
    }

    public function validateCallback(array $payload): bool
    {
        // iPaymu doesn't provide callback signature verification by default
        // Additional validation: check with checkTransaction API
        return true;
    }

    // =========================================================================
    // Payment Methods
    // =========================================================================

    public function getPaymentMethods(): array
    {
        return [
            'va' => [
                'label'    => 'Virtual Account',
                'channels' => ['bca', 'bni', 'bri', 'mandiri', 'cimb'],
            ],
            'qris' => [
                'label'    => 'QRIS',
                'channels' => ['qris'],
            ],
            'convenience_store' => [
                'label'    => 'Convenience Store',
                'channels' => ['alfamart', 'indomaret'],
            ],
            'ewallet' => [
                'label'    => 'E-Wallet',
                'channels' => ['gopay', 'ovo', 'dana'],
            ],
        ];
    }

    public function calculateAdminFee(int $amount): int
    {
        // iPaymu: flat fee Rp 1.500 for VA; 0.7% for QRIS
        return 1500;
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    private function generateHeaders(array $body): array
    {
        $va = $this->config['va'] ?? '';
        $secret = $this->config['api_key'] ?? '';
        $method = 'POST';
        $timestamp = now()->format('YmdHis');
        $bodyEncoded = json_encode($body);
        $bodyHash = hash('sha256', $bodyEncoded);
        $stringToSign = "{$method}:{$va}:{$bodyHash}:{$timestamp}";
        $signature = hash_hmac('sha256', $stringToSign, $secret);

        return [
            'Content-Type' => 'application/json',
            'va'           => $va,
            'signature'    => $signature,
            'timestamp'    => $timestamp,
        ];
    }
}

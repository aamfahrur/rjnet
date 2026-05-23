<?php

declare(strict_types=1);

namespace App\Services\Payment\Drivers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Payment\PaymentGatewayInterface;
use App\Traits\Loggable;
use Illuminate\Support\Facades\Http;

/**
 * Midtrans Payment Gateway Driver.
 *
 * @see https://docs.midtrans.com
 */
class MidtransDriver implements PaymentGatewayInterface
{
    use Loggable;

    private array $config;
    private string $baseUrl;
    private string $serverKey;
    private string $clientKey;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->baseUrl = $config['sandbox'] ?? true
            ? 'https://api.sandbox.midtrans.com/v2'
            : 'https://api.midtrans.com/v2';
        $this->serverKey = $config['server_key'] ?? '';
        $this->clientKey = $config['client_key'] ?? '';
    }

    protected function logPrefix(): string
    {
        return 'Midtrans';
    }

    public function getGatewayName(): string
    {
        return 'Midtrans';
    }

    public function isSandbox(): bool
    {
        return $this->config['sandbox'] ?? true;
    }

    public function createTransaction(Payment $payment, Invoice $invoice, array $params = []): array
    {
        $customer = $invoice->customer;

        $orderId = $payment->payment_number;
        $grossAmount = $payment->total_amount;

        $body = [
            'payment_type'        => $params['method'] ?? 'bank_transfer',
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $customer->full_name,
                'email'      => $customer->email ?? '',
                'phone'      => $customer->phone,
            ],
            'callbacks' => [
                'finish' => $params['return_url'] ?? route('customer.invoices.show', $invoice),
            ],
        ];

        // Add bank transfer details
        if (($params['method'] ?? 'bank_transfer') === 'bank_transfer') {
            $body['bank_transfer'] = [
                'bank' => $params['bank'] ?? 'bca',
            ];
        }

        $auth = base64_encode($this->serverKey . ':');

        try {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Basic ' . $auth,
            ])->post("{$this->baseUrl}/charge", $body);

            $result = $response->json();

            if ($response->successful() && !isset($result['error_messages'])) {
                $this->logInfo('Transaction created', [
                    'order_id' => $orderId,
                    'trx_id'   => $result['transaction_id'] ?? null,
                ]);

                return [
                    'success'        => true,
                    'transaction_id' => $result['transaction_id'] ?? $orderId,
                    'va_number'      => $result['va_numbers'][0]['va_number'] ??
                        $result['permata_va_number'] ?? null,
                    'payment_url' => $result['redirect_url'] ?? null,
                    'raw'         => $result,
                ];
            }

            $this->logError('Transaction failed', ['response' => $result]);
            return [
                'success' => false,
                'error'   => $result['status_message'] ?? ($result['error_messages'][0] ?? 'Unknown error'),
                'raw'     => $result,
            ];
        } catch (\Exception $e) {
            $this->logError('Transaction request failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function checkTransaction(string $gatewayTransactionId): array
    {
        $auth = base64_encode($this->serverKey . ':');

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $auth,
        ])->get("{$this->baseUrl}/{$gatewayTransactionId}/status");

        return $response->json();
    }

    public function handleCallback(array $payload): array
    {
        $transactionStatus = $payload['transaction_status'] ?? '';
        $orderId = $payload['order_id'] ?? '';
        $transactionId = $payload['transaction_id'] ?? '';

        return [
            'transaction_id' => $transactionId,
            'reference_id'   => $orderId,
            'status'         => in_array($transactionStatus, ['capture', 'settlement']) ? 'success' : 'pending',
            'paid_by'        => $payload['payment_type'] ?? 'unknown',
            'paid_amount'    => (int) ($payload['gross_amount'] ?? 0),
            'raw'            => $payload,
        ];
    }

    public function validateCallback(array $payload): bool
    {
        // Midtrans signature: sha512(order_id + status_code + gross_amount + server_key)
        $signatureKey = hash(
            'sha512',
            ($payload['order_id'] ?? '')
            . ($payload['status_code'] ?? '')
            . ($payload['gross_amount'] ?? '')
            . $this->serverKey
        );
        return $signatureKey === ($payload['signature_key'] ?? '');
    }

    public function getPaymentMethods(): array
    {
        return [
            'bank_transfer' => [
                'label'    => 'Bank Transfer',
                'channels' => ['bca', 'bni', 'bri', 'mandiri', 'permata'],
            ],
            'echannel' => [
                'label'    => 'Mandiri Bill',
                'channels' => [],
            ],
            'gopay' => [
                'label'    => 'GoPay',
                'channels' => [],
            ],
            'shopeepay' => [
                'label'    => 'ShopeePay',
                'channels' => [],
            ],
            'qris' => [
                'label'    => 'QRIS',
                'channels' => [],
            ],
        ];
    }

    public function calculateAdminFee(int $amount): int
    {
        return 4000; // Bank transfer admin fee
    }
}

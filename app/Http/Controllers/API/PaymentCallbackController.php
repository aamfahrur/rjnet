<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
    ) {
    }

    /**
     * Handle payment callback from all gateways.
     * POST /api/payment/callback/{gateway}
     */
    public function handle(string $gateway, Request $request): JsonResponse
    {
        try {
            $result = $this->paymentService->processCallback($gateway, $request->all());

            if ($result['success'] ?? false) {
                return response()->json(['status' => 'ok']);
            }

            return response()->json(['status' => 'error', 'message' => $result['error'] ?? 'Unknown error'], 400);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Payment callback error', [
                'gateway' => $gateway,
                'error'   => $e->getMessage(),
                'payload' => $request->all(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Internal error'], 500);
        }
    }
}

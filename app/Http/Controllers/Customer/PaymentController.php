<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PaymentGatewayConfig;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService)
    {
    }

    public function create(Invoice $invoice): Response
    {
        $gateways = $this->paymentService->getActiveGateways();
        return Inertia::render('Customer/Payments/Create', ['invoice' => $invoice, 'gateways' => $gateways]);
    }

    public function history(): Response
    {
        $customer = auth()->user()->customer;
        $payments = $customer?->payments()->with('invoice')->latest()->paginate(12) ?? collect();
        return Inertia::render('Customer/Payments/History', ['payments' => $payments]);
    }

    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        $gateway = PaymentGatewayConfig::findOrFail($request->input('payment_gateway_id'));

        $payment = $this->paymentService->createPayment($invoice, $gateway, $request->all());

        if ($payment->payment_url) {
            return redirect()->away($payment->payment_url);
        }

        return redirect()->route('customer.invoices.show', $invoice)
            ->with('info', 'Silakan selesaikan pembayaran: ' . $payment->va_number);
    }
}

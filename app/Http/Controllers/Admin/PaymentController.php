<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(): Response
    {
        $payments = Payment::with(['invoice', 'customer'])
            ->latest()
            ->paginate(25);

        return Inertia::render('Admin/Payments/Index', ['payments' => $payments]);
    }

    public function show(Payment $payment): Response
    {
        $payment->load(['invoice.customer', 'gateway', 'logs']);
        return Inertia::render('Admin/Payments/Show', ['payment' => $payment]);
    }

    public function manualConfirm(Payment $payment): RedirectResponse
    {
        $payment->markAsSuccess(['manual' => true], 'Manual by admin');
        return back()->with('success', 'Pembayaran dikonfirmasi manual.');
    }
}

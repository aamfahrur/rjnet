<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function index(): Response
    {
        $invoices = Invoice::with('customer')
            ->latest()
            ->paginate(25);

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $invoices,
        ]);
    }

    public function show(Invoice $invoice): Response
    {
        $invoice->load(['customer', 'items', 'payments']);
        return Inertia::render('Admin/Invoices/Show', ['invoice' => $invoice]);
    }

    public function generateInvoices(): RedirectResponse
    {
        \App\Jobs\GenerateMonthlyInvoices::dispatch();
        return back()->with('success', 'Invoice generation dispatched.');
    }
}

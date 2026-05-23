<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function index(): Response
    {
        $customer = auth()->user()->customer;
        $invoices = $customer?->invoices()->latest()->paginate(12) ?? collect();

        return Inertia::render('Customer/Invoices/Index', ['invoices' => $invoices]);
    }

    public function show(Invoice $invoice): Response
    {
        $invoice->load(['items', 'payments']);
        return Inertia::render('Customer/Invoices/Show', ['invoice' => $invoice]);
    }

    public function downloadPdf(Invoice $invoice): mixed
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', ['invoice' => $invoice]);
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\Customer\CustomerService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService,
    ) {
    }

    /**
     * Display customers listing.
     */
    public function index(): Response
    {
        $customers = Customer::query()
            ->with(['addresses', 'activeSubscription.package', 'activeSubscription.router'])
            ->withCount(['invoices as overdue_count' => fn ($q) => $q->where('status', \App\Enums\InvoiceStatus::OVERDUE->value)])
            ->when(request('search'), fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('customer_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when(request('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Admin/Customers/Index', [
            'customers' => CustomerResource::collection($customers),
            'filters'   => [
                'search' => request('search', ''),
                'status' => request('status', ''),
            ],
        ]);
    }

    /**
     * Show customer detail.
     */
    public function show(Customer $customer): Response
    {
        $customer->load([
            'addresses',
            'documents',
            'subscriptions.package',
            'subscriptions.router',
            'subscriptions.pppoeAccount',
            'pppoeAccounts.router',
            'invoices' => fn ($q) => $q->latest()->limit(12),
            'payments' => fn ($q) => $q->latest()->limit(12),
            'tickets'  => fn ($q) => $q->latest()->limit(10),
        ]);

        return Inertia::render('Admin/Customers/Show', [
            'customer' => new CustomerResource($customer),
        ]);
    }

    /**
     * Store new customer.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = $this->customerService->registerCustomer(
            $request->validated(),
            $request->validated('address'),
        );

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Pelanggan berhasil didaftarkan.');
    }

    /**
     * Update customer.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return back()->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    /**
     * Suspend customer.
     */
    public function suspend(Customer $customer): RedirectResponse
    {
        $this->customerService->suspendCustomer($customer, request('reason', 'Administrative suspension'));
        return back()->with('success', 'Pelanggan berhasil di-suspend.');
    }

    /**
     * Unsuspend customer.
     */
    public function unsuspend(Customer $customer): RedirectResponse
    {
        $this->customerService->unsuspendCustomer($customer);
        return back()->with('success', 'Pelanggan berhasil di-unsuspend.');
    }

    /**
     * Delete customer.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();
        return redirect()->route('admin.customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }
}

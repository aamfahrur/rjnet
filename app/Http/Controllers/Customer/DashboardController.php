<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $customer = $user->customer;

        return Inertia::render('Customer/Dashboard', [
            'customer' => $customer?->load(['activeSubscription.package', 'invoices' => fn ($q) => $q->latest()->limit(5)]),
        ]);
    }
}

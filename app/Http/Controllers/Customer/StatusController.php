<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class StatusController extends Controller
{
    public function index(): Response
    {
        $customer = auth()->user()->customer;
        $subscription = $customer?->activeSubscription;
        $pppoe = $subscription?->pppoeAccount;

        // Check if user is online via Mikrotik
        $isOnline = false;
        if ($pppoe) {
            try {
                $pppoeService = app(\App\Services\Mikrotik\MikrotikServiceFactory::class)->pppoe($pppoe->router);
                $isOnline = $pppoeService->isUserOnline($pppoe->username);
            } catch (\Exception) {
            }
        }

        return Inertia::render('Customer/Status', [
            'subscription' => $subscription?->load('package'),
            'pppoeAccount' => $pppoe,
            'isOnline'     => $isOnline,
        ]);
    }
}

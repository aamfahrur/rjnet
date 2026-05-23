<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        $user = auth()->user()->load('customer.activeSubscription.pppoeAccount');
        return Inertia::render('Customer/Profile/Edit', ['user' => $user]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'name'             => 'string|max:255',
            'phone'            => 'nullable|string|max:20',
            'telegram_chat_id' => 'nullable|string',
        ]);

        auth()->user()->update($request->only(['name', 'phone', 'telegram_chat_id']));

        return back()->with('success', 'Profil diperbarui.');
    }

    public function changePPPoEPassword(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|string|min:6|confirmed']);

        $subscription = auth()->user()->customer?->activeSubscription;
        if (!$subscription?->pppoeAccount) {
            return back()->with('error', 'Tidak ada akun PPPoE.');
        }

        app(\App\Services\Customer\CustomerService::class)->changePPPoEPassword(
            $subscription,
            $request->input('password')
        );

        return back()->with('success', 'Password PPPoE berhasil diubah.');
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGatewayConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentGatewayConfigController extends Controller
{
    public function index(): Response
    {
        $gateways = PaymentGatewayConfig::all();
        return Inertia::render('Admin/Gateways/Index', ['gateways' => $gateways]);
    }

    public function store(Request $request): RedirectResponse
    {
        PaymentGatewayConfig::create($request->validate([
            'name'       => 'required|string|max:50',
            'code'       => 'required|string|max:30|unique:payment_gateways',
            'is_active'  => 'boolean',
            'is_sandbox' => 'boolean',
        ]));

        return back()->with('success', 'Gateway ditambahkan.');
    }

    public function update(Request $request, PaymentGatewayConfig $gateway): RedirectResponse
    {
        $gateway->update($request->only(['name', 'config', 'is_active', 'is_sandbox', 'admin_fee_percentage', 'admin_fee_fixed']));
        return back()->with('success', 'Gateway diperbarui.');
    }

    public function destroy(PaymentGatewayConfig $gateway): RedirectResponse
    {
        $gateway->delete();
        return back()->with('success', 'Gateway dihapus.');
    }
}

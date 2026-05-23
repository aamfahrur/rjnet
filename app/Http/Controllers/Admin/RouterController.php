<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\CollectRouterMetrics;
use App\Models\Router;
use App\Services\Mikrotik\MikrotikServiceFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RouterController extends Controller
{
    public function __construct(
        private readonly MikrotikServiceFactory $mikrotikFactory,
    ) {
    }

    /**
     * List all routers.
     */
    public function index(): Response
    {
        $routers = Router::with(['group', 'metrics' => fn ($q) => $q->latest()->limit(1)])
            ->withCount('pppoeAccounts')
            ->latest()
            ->paginate(25);

        return Inertia::render('Admin/Routers/Index', [
            'routers' => $routers,
        ]);
    }

    /**
     * Show router detail with realtime metrics.
     */
    public function show(Router $router): Response
    {
        $router->load(['group', 'pppoeAccounts.customer', 'hotspotAccounts.customer']);

        $recentMetrics = $router->metrics()
            ->latest('recorded_at')
            ->limit(60)
            ->get();

        $recentTraffic = $router->trafficLogs()
            ->latest('recorded_at')
            ->limit(10)
            ->get();

        return Inertia::render('Admin/Routers/Show', [
            'router'        => $router,
            'recentMetrics' => $recentMetrics,
            'recentTraffic' => $recentTraffic,
            'onlineUsers'   => $router->onlineSessions()->where('recorded_at', '>=', now()->subMinutes(5))->count(),
        ]);
    }

    /**
     * Store new router.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:100',
            'host'            => 'required|string|max:100',
            'api_port'        => 'integer|min:1|max:65535',
            'username'        => 'required|string|max:100',
            'password'        => 'nullable|string',
            'router_group_id' => 'nullable|exists:router_groups,id',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
            'is_active'       => 'boolean',
            'use_ssl'         => 'boolean',
        ]);

        $router = Router::create($validated);

        return redirect()->route('admin.routers.show', $router)
            ->with('success', 'Router berhasil ditambahkan.');
    }

    /**
     * Update router.
     */
    public function update(Request $request, Router $router): RedirectResponse
    {
        $validated = $request->validate([
            'name'            => 'string|max:100',
            'host'            => 'string|max:100',
            'api_port'        => 'integer|min:1|max:65535',
            'username'        => 'string|max:100',
            'password'        => 'nullable|string',
            'router_group_id' => 'nullable|exists:router_groups,id',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
            'is_active'       => 'boolean',
            'use_ssl'         => 'boolean',
            'notes'           => 'nullable|string',
        ]);

        $router->update($validated);

        return back()->with('success', 'Router berhasil diperbarui.');
    }

    /**
     * Test connection to router.
     */
    public function testConnection(Router $router): \Illuminate\Http\JsonResponse
    {
        try {
            $monitoring = $this->mikrotikFactory->monitoring($router);
            $identity = $monitoring->getSystemIdentity();

            return response()->json([
                'success'  => true,
                'identity' => $identity,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Collect metrics now.
     */
    public function collectMetrics(Router $router): RedirectResponse
    {
        CollectRouterMetrics::dispatch($router);
        return back()->with('success', 'Metrics collection dispatched.');
    }

    /**
     * Delete router.
     */
    public function destroy(Router $router): RedirectResponse
    {
        $router->delete();
        return redirect()->route('admin.routers.index')
            ->with('success', 'Router berhasil dihapus.');
    }
}

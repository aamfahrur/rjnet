<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\InternetPackage;
use App\Models\Invoice;
use App\Models\Router;
use App\Models\Subscription;
use App\Models\Ticket;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    /**
     * Admin dashboard - overview of the entire system.
     */
    public function index(): Response
    {
        $stats = [
            'total_customers'      => Customer::count(),
            'active_customers'     => Customer::active()->count(),
            'suspended_customers'  => Customer::suspended()->count(),
            'total_routers'        => Router::count(),
            'online_routers'       => Router::online()->count(),
            'total_packages'       => InternetPackage::count(),
            'active_subscriptions' => Subscription::active()->count(),
            'pending_invoices'     => Invoice::pending()->count(),
            'overdue_invoices'     => Invoice::overdue()->count(),
            'revenue_this_month'   => $this->getMonthlyRevenue(),
            'open_tickets'         => Ticket::open()->count(),
            'online_users'         => $this->getOnlineUsersEstimate(),
        ];

        $recentActivity = $this->getRecentActivity();

        return Inertia::render('Admin/Dashboard', [
            'stats'          => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }

    private function getMonthlyRevenue(): int
    {
        return Invoice::paid()
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_amount');
    }

    private function getOnlineUsersEstimate(): int
    {
        return \App\Models\OnlineSession::where('recorded_at', '>=', now()->subMinutes(5))->count();
    }

    private function getRecentActivity(): array
    {
        return Activity::latest()
            ->with('causer')
            ->limit(10)
            ->get()
            ->toArray();
    }
}

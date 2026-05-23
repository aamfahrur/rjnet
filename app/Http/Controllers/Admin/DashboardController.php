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

        return Inertia::render('Admin/Dashboard', [
            'stats'             => $stats,
            'revenue_chart'     => $this->getRevenueChart(),
            'customer_growth'   => $this->getCustomerGrowth(),
            'recent_activities' => $this->getRecentActivities(),
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

    /**
     * Get revenue chart data for the last 6 months.
     */
    private function getRevenueChart(): array
    {
        $months = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->translatedFormat('M');
            $data[] = Invoice::paid()
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('total_amount');
        }

        return array_map(fn ($month, $amount) => ['month' => $month, 'amount' => (int) $amount], $months, $data);
    }

    /**
     * Get customer growth chart data for the last 6 months.
     */
    private function getCustomerGrowth(): array
    {
        $months = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->translatedFormat('M');
            $data[] = Customer::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        }

        return array_map(fn ($month, $count) => ['month' => $month, 'count' => $count], $months, $data);
    }

    /**
     * Get recent activities formatted for the frontend.
     */
    private function getRecentActivities(): array
    {
        return Activity::latest()
            ->with('causer')
            ->limit(8)
            ->get()
            ->map(fn ($activity) => [
                'id'          => $activity->id,
                'description' => $activity->description,
                'created_at'  => $activity->created_at->diffForHumans(),
            ])
            ->toArray();
    }
}

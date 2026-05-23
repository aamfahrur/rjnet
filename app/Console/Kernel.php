<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by the application.
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * All scheduled tasks for the RT/RW Net billing system.
     */
    protected function schedule(Schedule $schedule): void
    {
        // =========================================================================
        // Billing Schedule
        // =========================================================================

        // Generate monthly invoices - runs daily at 00:05
        $schedule->job(new \App\Jobs\GenerateMonthlyInvoices())
            ->dailyAt('00:05')
            ->withoutOverlapping()
            ->onOneServer()
            ->name('billing:generate-monthly-invoices');

        // Mark overdue invoices - runs daily at 00:10
        $schedule->call(function () {
            app(\App\Services\Billing\BillingService::class)->markOverdueInvoices();
        })->dailyAt('00:10')
            ->name('billing:mark-overdue');

        // =========================================================================
        // Reminder Schedule
        // =========================================================================

        // H-7 reminder
        $schedule->job(new \App\Jobs\SendBillingReminders(7))
            ->dailyAt('07:00')
            ->name('billing:reminder-h7');

        // H-3 reminder
        $schedule->job(new \App\Jobs\SendBillingReminders(3))
            ->dailyAt('07:00')
            ->name('billing:reminder-h3');

        // H-1 reminder
        $schedule->job(new \App\Jobs\SendBillingReminders(1))
            ->dailyAt('07:00')
            ->name('billing:reminder-h1');

        // =========================================================================
        // Auto Suspend
        // =========================================================================

        // Auto-suspend overdue customers (7 days grace)
        $schedule->job(new \App\Jobs\AutoSuspendOverdueCustomers(7))
            ->dailyAt('00:15')
            ->name('billing:auto-suspend');

        // Auto-unsuspend paid customers
        $schedule->call(function () {
            app(\App\Services\Billing\BillingService::class)->autoUnsuspendPaidCustomers();
        })->dailyAt('06:00')
            ->name('billing:auto-unsuspend');

        // =========================================================================
        // Monitoring Schedule
        // =========================================================================

        // Collect router metrics every 5 minutes for active routers
        $schedule->call(function () {
            \App\Models\Router::active()->each(function ($router) {
                \App\Jobs\CollectRouterMetrics::dispatch($router);
            });
        })->everyFiveMinutes()
            ->name('monitoring:collect-metrics');

        // =========================================================================
        // Housekeeping
        // =========================================================================

        // Cleanup old sessions and logs
        $schedule->command('activitylog:clean')->daily();
        $schedule->command('sanctum:prune-expired --hours=24')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes - RT/RW Net Management System
|--------------------------------------------------------------------------
*/

// =========================================================================
// Public Routes (No Auth)
// =========================================================================

Route::get('/', function () {
    return Inertia::render('Welcome');
});

// =========================================================================
// Authentication Routes
// =========================================================================

Route::prefix('auth')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])
        ->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
    Route::get('/forgot-password', fn () => Inertia::render('Auth/ForgotPassword'))->name('password.request');
    Route::get('/reset-password/{token}', fn () => Inertia::render('Auth/ResetPassword'))->name('password.reset');
});

// =========================================================================
// Authenticated Admin Routes
// =========================================================================

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // TODO: add 'role:admin|teknisi|cs,web' middleware after debugging

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Customer Management
    Route::get('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', fn () => Inertia::render('Admin/Customers/Create'))->name('customers.create');
    Route::post('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.show');
    Route::put('/customers/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('customers.update');
    Route::post('/customers/{customer}/suspend', [App\Http\Controllers\Admin\CustomerController::class, 'suspend'])->name('customers.suspend');
    Route::post('/customers/{customer}/unsuspend', [App\Http\Controllers\Admin\CustomerController::class, 'unsuspend'])->name('customers.unsuspend');
    Route::delete('/customers/{customer}', [App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('customers.destroy');

    // Internet Packages
    Route::resource('packages', App\Http\Controllers\Admin\PackageController::class)->except(['show']);

    // Router Management
    Route::get('/routers', [App\Http\Controllers\Admin\RouterController::class, 'index'])->name('routers.index');
    Route::get('/routers/create', fn () => Inertia::render('Admin/Routers/Create'))->name('routers.create');
    Route::post('/routers', [App\Http\Controllers\Admin\RouterController::class, 'store'])->name('routers.store');
    Route::get('/routers/{router}', [App\Http\Controllers\Admin\RouterController::class, 'show'])->name('routers.show');
    Route::put('/routers/{router}', [App\Http\Controllers\Admin\RouterController::class, 'update'])->name('routers.update');
    Route::post('/routers/{router}/test', [App\Http\Controllers\Admin\RouterController::class, 'testConnection'])->name('routers.test');
    Route::post('/routers/{router}/collect-metrics', [App\Http\Controllers\Admin\RouterController::class, 'collectMetrics'])->name('routers.collect-metrics');
    Route::delete('/routers/{router}', [App\Http\Controllers\Admin\RouterController::class, 'destroy'])->name('routers.destroy');

    // Invoices
    Route::get('/invoices', [App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('/invoices/generate', [App\Http\Controllers\Admin\InvoiceController::class, 'generateInvoices'])->name('invoices.generate');

    // Payments
    Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/confirm', [App\Http\Controllers\Admin\PaymentController::class, 'manualConfirm'])->name('payments.confirm');

    // Payment Gateways Config
    Route::resource('payment-gateways', App\Http\Controllers\Admin\PaymentGatewayConfigController::class)->except(['show']);

    // Ticket Management
    Route::get('/tickets', [App\Http\Controllers\Admin\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [App\Http\Controllers\Admin\TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [App\Http\Controllers\Admin\TicketController::class, 'reply'])->name('tickets.reply');
    Route::post('/tickets/{ticket}/assign', [App\Http\Controllers\Admin\TicketController::class, 'assign'])->name('tickets.assign');
    Route::post('/tickets/{ticket}/resolve', [App\Http\Controllers\Admin\TicketController::class, 'resolve'])->name('tickets.resolve');
    Route::post('/tickets/{ticket}/close', [App\Http\Controllers\Admin\TicketController::class, 'close'])->name('tickets.close');

    // Network Topology
    Route::get('/topology', [App\Http\Controllers\Admin\TopologyController::class, 'index'])->name('topology.index');
    Route::get('/topology/data', [App\Http\Controllers\Admin\TopologyController::class, 'data'])->name('topology.data');
    Route::post('/topology/nodes', [App\Http\Controllers\Admin\TopologyController::class, 'storeNode'])->name('topology.nodes.store');
    Route::post('/topology/links', [App\Http\Controllers\Admin\TopologyController::class, 'storeLink'])->name('topology.links.store');

    // Monitoring / NOC
    Route::get('/monitoring', [App\Http\Controllers\Admin\MonitoringController::class, 'index'])->name('monitoring.index');

    // Reports
    Route::get('/reports', fn () => Inertia::render('Admin/Reports'))->name('reports');

    // Settings
    Route::get('/settings', fn () => Inertia::render('Admin/Settings'))->name('settings');

    // Users / Staff Management
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);

    // Activity Log
    Route::get('/activity-logs', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs');
});

// =========================================================================
// Customer Panel Routes
// =========================================================================

Route::middleware(['auth', 'role:customer,web'])->prefix('panel')->name('customer.')->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('dashboard');

    // Invoices
    Route::get('/invoices', [App\Http\Controllers\Customer\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [App\Http\Controllers\Customer\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/pdf', [App\Http\Controllers\Customer\InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');

    // Payment
    Route::post('/invoices/{invoice}/pay', [App\Http\Controllers\Customer\PaymentController::class, 'create'])->name('invoices.pay');
    Route::get('/payments/history', [App\Http\Controllers\Customer\PaymentController::class, 'history'])->name('payments.history');

    // Profile
    Route::get('/profile', [App\Http\Controllers\Customer\ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-pppoe-password', [App\Http\Controllers\Customer\ProfileController::class, 'changePPPoEPassword'])->name('profile.change-pppoe-password');

    // Tickets
    Route::get('/tickets', [App\Http\Controllers\Customer\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [App\Http\Controllers\Customer\TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [App\Http\Controllers\Customer\TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [App\Http\Controllers\Customer\TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [App\Http\Controllers\Customer\TicketController::class, 'reply'])->name('tickets.reply');

    // Internet Status
    Route::get('/status', [App\Http\Controllers\Customer\StatusController::class, 'index'])->name('status');
});

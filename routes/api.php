<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - RT/RW Net Management System
|--------------------------------------------------------------------------
*/

// =========================================================================
// Public API
// =========================================================================

// Payment Gateway Callbacks (no auth required)
Route::post('/payment/callback/{gateway}', [App\Http\Controllers\API\PaymentCallbackController::class, 'handle'])
    ->name('api.payment.callback');

// =========================================================================
// Authenticated API (Sanctum)
// =========================================================================

Route::middleware('auth:sanctum')->group(function () {

    // Current user
    Route::get('/user', fn (\Illuminate\Http\Request $request) => $request->user());

    // Monitoring endpoints
    Route::prefix('monitoring')->name('api.monitoring.')->group(function () {
        Route::get('/routers/{router}/metrics', [App\Http\Controllers\API\MonitoringController::class, 'routerMetrics']);
        Route::get('/routers/{router}/online-users', [App\Http\Controllers\API\MonitoringController::class, 'onlineUsers']);
        Route::get('/routers/{router}/traffic-history', [App\Http\Controllers\API\MonitoringController::class, 'trafficHistory']);
    });

    // Topology data
    Route::get('/topology/nodes', [App\Http\Controllers\Admin\TopologyController::class, 'data']);

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\API\NotificationController::class, 'index']);
    Route::post('/notifications/{notification}/read', [App\Http\Controllers\API\NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [App\Http\Controllers\API\NotificationController::class, 'markAllRead']);
});

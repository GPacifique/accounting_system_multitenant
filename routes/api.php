<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API routes with authentication and rate limiting
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // User profile endpoint
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles');
    });
    
    // Dashboard API endpoints (temporarily disabled - controllers missing)
    Route::prefix('dashboard')->group(function () {
        // Route::get('/stats', [\App\Http\Controllers\Api\DashboardApiController::class, 'getStats']);
        // Route::get('/financial-summary', [\App\Http\Controllers\Api\DashboardApiController::class, 'getFinancialSummary']);
        // Route::get('/recent-activities', [\App\Http\Controllers\Api\DashboardApiController::class, 'getRecentActivities']);
    });
    
    // Business Data API endpoints (temporarily disabled - controllers missing)
    // Route::apiResource('projects', \App\Http\Controllers\Api\ProjectApiController::class);
    // Route::apiResource('tasks', \App\Http\Controllers\Api\TaskApiController::class);
    // Route::apiResource('expenses', \App\Http\Controllers\Api\ExpenseApiController::class);
    // Route::apiResource('incomes', \App\Http\Controllers\Api\IncomeApiController::class);
    // Route::apiResource('clients', \App\Http\Controllers\Api\ClientApiController::class);
    // Route::apiResource('payments', \App\Http\Controllers\Api\PaymentApiController::class);
    
    // Reports API (temporarily disabled - controllers missing)
    Route::prefix('reports')->group(function () {
        // Route::get('/financial', [\App\Http\Controllers\Api\ReportApiController::class, 'financial']);
        // Route::get('/project-summary', [\App\Http\Controllers\Api\ReportApiController::class, 'projectSummary']);
        // Route::get('/cash-flow', [\App\Http\Controllers\Api\ReportApiController::class, 'cashFlow']);
    });
    
    // System management endpoints (Admin only) - temporarily disabled
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        // Route::apiResource('users', \App\Http\Controllers\Api\UserApiController::class);
        // Route::apiResource('tenants', \App\Http\Controllers\Api\TenantApiController::class);
        // Route::get('/audit-logs', [\App\Http\Controllers\Api\AuditApiController::class, 'index']);
        // Route::post('/backup', [\App\Http\Controllers\Api\SystemApiController::class, 'createBackup']);
        // Route::get('/system-status', [\App\Http\Controllers\Api\SystemApiController::class, 'getStatus']);
    });
});

// Public API routes (if needed) with aggressive rate limiting
Route::middleware(['throttle:public_api'])->group(function () {
    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'version' => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
        ]);
    });
    
    // Public statistics (non-sensitive data only)
    Route::get('/public/stats', function () {
        return response()->json([
            'total_projects' => \App\Models\Project::count(),
            'system_uptime' => now()->diffForHumans(\Illuminate\Support\Carbon::createFromTimestamp(filemtime(base_path()))),
        ]);
    });
});

// Webhook endpoints (for external integrations)
Route::prefix('webhooks')->middleware(['throttle:webhook'])->group(function () {
    // Add webhook endpoints here for external service integrations
    // Example: Route::post('/payment-notification', [WebhookController::class, 'handlePayment']);
});
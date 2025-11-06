<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminTenantController;
use App\Http\Controllers\Admin\AdminSubscriptionController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\AdminDataController;
use App\Http\Controllers\Admin\AdminBackupController;
use App\Http\Controllers\Admin\AdminDatabaseController;
use App\Http\Controllers\Admin\AdminApiController;
use App\Http\Controllers\Admin\AdminWebhookController;
use App\Http\Controllers\Admin\TenantSubscriptionController;
use App\Http\Controllers\Admin\TenantAuditLogController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Role Switcher Routes
    Route::post('/switch-role/{role}', [RoleSwitcherController::class, 'switch'])->name('role.switch');
    Route::post('/clear-role', [RoleSwitcherController::class, 'clear'])->name('role.clear');
    
    // Welcome routes for users with no permissions
    Route::get('/welcome-user', [WelcomeController::class, 'index'])->name('welcome.index');
    Route::get('/welcome-user/request-access', [WelcomeController::class, 'requestAccess'])->name('welcome.request-access');
    Route::post('/welcome-user/request-access', [WelcomeController::class, 'submitAccessRequest'])->name('welcome.submit-access-request');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Role-specific dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/manager/dashboard', [DashboardController::class, 'index'])->name('manager.dashboard');
    Route::get('/accountant/dashboard', [DashboardController::class, 'index'])->name('accountant.dashboard');
    Route::get('/user/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');
});

// ============================================
// AUTHENTICATED & VERIFIED ROUTES
// ============================================
Route::middleware(['auth', 'verified', 'check.permissions'])->group(function () {
    
    Route::get('/profile', function () {
        return view('profile.show');
    })->name('profile.show');
    
    // ============ ADMIN ONLY ============
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
    
    // ============ MANAGER & ADMIN ============
    Route::middleware(['role:admin|manager'])->group(function () {
        Route::resource('projects', ProjectController::class);
        // Project downloads
        Route::get('projects/export/csv', [ProjectController::class, 'exportCsv'])->name('projects.export.csv');
        Route::get('projects/export/pdf', [ProjectController::class, 'exportPdf'])->name('projects.export.pdf');
        
        Route::resource('employees', EmployeeController::class);
        Route::resource('workers', WorkerController::class);
        // Worker downloads
        Route::get('workers/export/csv', [WorkerController::class, 'exportCsv'])->name('workers.export.csv');
        Route::get('workers/export/pdf', [WorkerController::class, 'exportPdf'])->name('workers.export.pdf');
        
        // Daily payments for casual workers
        Route::post('workers/payments/bulk', [WorkerController::class, 'bulkStorePayments'])->name('workers.payments.bulk');
        Route::resource('orders', OrderController::class);
        Route::post('orders/{order}/items', [OrderController::class, 'addItem'])->name('orders.items.add');
        Route::delete('orders/{order}/items/{item}', [OrderController::class, 'removeItem'])->name('orders.items.remove');
        Route::post('orders/{order}/pay', [OrderController::class, 'markAsPaid'])->name('orders.pay');
    });
    
    // ============ ACCOUNTANT & ADMIN ============
    Route::middleware(['role:admin|accountant'])->group(function () {
        Route::resource('expenses', ExpenseController::class);
        // Expense downloads
        Route::get('expenses/export/csv', [ExpenseController::class, 'exportCsv'])->name('expenses.export.csv');
        Route::get('expenses/export/pdf', [ExpenseController::class, 'exportPdf'])->name('expenses.export.pdf');
        
        Route::resource('incomes', IncomeController::class);
        // Income downloads
        Route::get('incomes/export/csv', [IncomeController::class, 'exportCsv'])->name('incomes.export.csv');
        Route::get('incomes/export/pdf', [IncomeController::class, 'exportPdf'])->name('incomes.export.pdf');
        
        Route::resource('payments', PaymentController::class);
        // Payment downloads
        Route::get('payments/export/csv', [PaymentController::class, 'exportCsv'])->name('payments.export.csv');
        Route::get('payments/export/pdf', [PaymentController::class, 'exportPdf'])->name('payments.export.pdf');
    });
    
    // ============ EVERYONE (Authenticated) ============
    Route::resource('reports', ReportController::class);
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    // Report downloads
    Route::get('reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    
    Route::resource('clients', ClientController::class);
    Route::resource('transactions', TransactionController::class);
    // Transaction downloads
    Route::get('transactions/export/csv', [TransactionController::class, 'exportCsv'])->name('transactions.export.csv');
    Route::get('transactions/export/pdf', [TransactionController::class, 'exportPdf'])->name('transactions.export.pdf');
    
    Route::resource('tasks', \App\Http\Controllers\TaskController::class);
    // Task downloads
    Route::get('tasks/export/csv', [\App\Http\Controllers\TaskController::class, 'exportCsv'])->name('tasks.export.csv');
    Route::get('tasks/export/pdf', [\App\Http\Controllers\TaskController::class, 'exportPdf'])->name('tasks.export.pdf');
    
    Route::resource('finance', FinanceController::class);
    Route::resource('products', ProductController::class);
    // Product downloads
    Route::get('products/export/csv', [ProductController::class, 'exportCsv'])->name('products.export.csv');
    Route::get('products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
});

// ============ TENANT INVITATIONS ============
Route::middleware(['auth'])->prefix('admin/tenants/{tenant}/invitations')->name('admin.tenants.invitations.')->group(function () {
    Route::get('/', [\App\Http\Controllers\TenantInvitationController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\TenantInvitationController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\TenantInvitationController::class, 'store'])->name('store');
});

Route::middleware(['auth'])->prefix('admin/invitations')->name('admin.invitations.')->group(function () {
    Route::post('{invitation}/resend', [\App\Http\Controllers\TenantInvitationController::class, 'resend'])->name('resend');
    Route::post('{invitation}/cancel', [\App\Http\Controllers\TenantInvitationController::class, 'cancel'])->name('cancel');
});

// Public invitation routes (no auth required for viewing)
Route::prefix('invitations')->name('invitations.')->group(function () {
    Route::get('{token}', [\App\Http\Controllers\TenantInvitationController::class, 'show'])->name('show');
    Route::post('{token}/accept', [\App\Http\Controllers\TenantInvitationController::class, 'accept'])->name('accept')->middleware('auth');
    Route::get('{token}/decline', [\App\Http\Controllers\TenantInvitationController::class, 'decline'])->name('decline');
});

// ============ TENANT SWITCHING ============
Route::middleware(['auth'])->group(function () {
    Route::post('/switch-tenant/{tenant}', [\App\Http\Controllers\TenantController::class, 'switchTenant'])
         ->name('tenant.switch');
    Route::get('/tenant-dashboard', [\App\Http\Controllers\TenantController::class, 'dashboard'])
         ->name('tenant.dashboard');
});

// ============ SUPER ADMIN TENANT MANAGEMENT ============
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('tenants', \App\Http\Controllers\TenantController::class);
    Route::post('tenants/{tenant}/toggle-status', [\App\Http\Controllers\TenantController::class, 'toggleStatus'])
         ->name('tenants.toggle-status');
    Route::get('tenants/{tenant}/users', [\App\Http\Controllers\TenantController::class, 'users'])
         ->name('tenants.users');
    Route::post('tenants/{tenant}/invite-user', [\App\Http\Controllers\TenantController::class, 'inviteUser'])
         ->name('tenants.invite-user');
    Route::get('tenants/{tenant}/settings', [\App\Http\Controllers\TenantController::class, 'settings'])
         ->name('tenants.settings');
    Route::put('tenants/{tenant}/settings', [\App\Http\Controllers\TenantController::class, 'updateSettings'])
         ->name('tenants.update-settings');
    Route::post('tenants/{tenant}/backup', [\App\Http\Controllers\TenantController::class, 'backup'])
         ->name('tenants.backup');
    Route::get('tenants/export/{format}', [\App\Http\Controllers\TenantController::class, 'export'])
         ->name('tenants.export');
    Route::get('analytics', [\App\Http\Controllers\TenantController::class, 'analytics'])
         ->name('analytics');
    Route::get('analytics/export', [\App\Http\Controllers\TenantController::class, 'exportAnalytics'])
         ->name('analytics.export');
    
    // ============ SUPER ADMIN EXCLUSIVE ROUTES ============
    
    // Tenant Invitations Management (Already working)
    Route::resource('invitations', \App\Http\Controllers\TenantInvitationController::class);
    
    // Tenant Subscriptions Management
    Route::resource('subscriptions', \App\Http\Controllers\Admin\TenantSubscriptionController::class);
    Route::patch('subscriptions/{subscription}/upgrade', [\App\Http\Controllers\Admin\TenantSubscriptionController::class, 'upgrade'])->name('subscriptions.upgrade');
    Route::patch('subscriptions/{subscription}/suspend', [\App\Http\Controllers\Admin\TenantSubscriptionController::class, 'suspend'])->name('subscriptions.suspend');
    Route::patch('subscriptions/{subscription}/resume', [\App\Http\Controllers\Admin\TenantSubscriptionController::class, 'resume'])->name('subscriptions.resume');
    Route::patch('subscriptions/{subscription}/cancel', [\App\Http\Controllers\Admin\TenantSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::patch('subscriptions/{subscription}/renew', [\App\Http\Controllers\Admin\TenantSubscriptionController::class, 'renew'])->name('subscriptions.renew');
    
    // Audit Logs Management
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TenantAuditLogController::class, 'index'])->name('index');
        Route::get('/{auditLog}', [\App\Http\Controllers\Admin\TenantAuditLogController::class, 'show'])->name('show');
        Route::get('/export/csv', [\App\Http\Controllers\Admin\TenantAuditLogController::class, 'export'])->name('export');
        Route::get('/stats/api', [\App\Http\Controllers\Admin\TenantAuditLogController::class, 'stats'])->name('stats');
        Route::get('/cleanup/form', [\App\Http\Controllers\Admin\TenantAuditLogController::class, 'cleanupForm'])->name('cleanup.form');
        Route::delete('/cleanup', [\App\Http\Controllers\Admin\TenantAuditLogController::class, 'cleanup'])->name('cleanup');
    });
    
    // Admin Settings Management
    Route::prefix('admin/settings')->name('admin.settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'index'])->name('index');
        Route::patch('/app', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'updateApp'])->name('update.app');
        Route::patch('/mail', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'updateMail'])->name('update.mail');
        Route::patch('/cache', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'updateCache'])->name('update.cache');
        Route::post('/mail/test', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'testMail'])->name('mail.test');
        Route::post('/cache/clear', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'clearCache'])->name('cache.clear');
        Route::get('/maintenance', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'maintenance'])->name('maintenance');
        Route::post('/maintenance/enable', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'enableMaintenance'])->name('maintenance.enable');
        Route::post('/maintenance/disable', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'disableMaintenance'])->name('maintenance.disable');
        Route::get('/optimization', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'optimization'])->name('optimization');
        Route::post('/optimize', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'optimize'])->name('optimize');
        Route::post('/optimize/clear', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'clearOptimization'])->name('optimize.clear');
    });
    
    // System Logs Management
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminLogController::class, 'index'])->name('index');
        Route::get('/{filename}', [\App\Http\Controllers\Admin\AdminLogController::class, 'show'])->name('show');
        Route::get('/{filename}/download', [\App\Http\Controllers\Admin\AdminLogController::class, 'download'])->name('download');
        Route::delete('/{filename}', [\App\Http\Controllers\Admin\AdminLogController::class, 'delete'])->name('delete');
        Route::delete('/', [\App\Http\Controllers\Admin\AdminLogController::class, 'clear'])->name('clear');
        Route::post('/search', [\App\Http\Controllers\Admin\AdminLogController::class, 'search'])->name('search');
    });
    
    // Data Management
    Route::prefix('data')->name('data.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminDataController::class, 'index'])->name('index');
        Route::post('/export', [\App\Http\Controllers\Admin\AdminDataController::class, 'export'])->name('export');
        Route::post('/import', [\App\Http\Controllers\Admin\AdminDataController::class, 'import'])->name('import');
    });
    
    // Backup Management
    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminBackupController::class, 'index'])->name('index');
        Route::post('/create', [\App\Http\Controllers\Admin\AdminBackupController::class, 'create'])->name('create');
        Route::get('/{filename}/download', [\App\Http\Controllers\Admin\AdminBackupController::class, 'download'])->name('download');
        Route::delete('/{filename}', [\App\Http\Controllers\Admin\AdminBackupController::class, 'destroy'])->name('destroy');
        Route::post('/{filename}/restore', [\App\Http\Controllers\Admin\AdminBackupController::class, 'restore'])->name('restore');
        Route::post('/schedule', [\App\Http\Controllers\Admin\AdminBackupController::class, 'schedule'])->name('schedule');
    });
    
    // Placeholder routes for remaining features - these will use simple controllers
    Route::prefix('database')->name('database.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminDatabaseController::class, 'index'])->name('index');
        Route::get('/tables/{table}', [\App\Http\Controllers\Admin\AdminDatabaseController::class, 'showTable'])->name('table.show');
        Route::post('/query', [\App\Http\Controllers\Admin\AdminDatabaseController::class, 'executeQuery'])->name('query.execute');
        Route::post('/optimize', [\App\Http\Controllers\Admin\AdminDatabaseController::class, 'optimize'])->name('optimize');
        Route::post('/migrate', [\App\Http\Controllers\Admin\AdminDatabaseController::class, 'migrate'])->name('migrate');
        Route::post('/seed', [\App\Http\Controllers\Admin\AdminDatabaseController::class, 'seed'])->name('seed');
        Route::get('/query-builder', [\App\Http\Controllers\Admin\AdminDatabaseController::class, 'queryBuilder'])->name('query-builder');
        Route::get('/tables/{table}/export', [\App\Http\Controllers\Admin\AdminDatabaseController::class, 'exportTable'])->name('table.export');
    });
    
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminApiController::class, 'index'])->name('index');
        Route::post('/keys', [\App\Http\Controllers\Admin\AdminApiController::class, 'createKey'])->name('keys.create');
        Route::put('/keys/{keyId}', [\App\Http\Controllers\Admin\AdminApiController::class, 'updateKey'])->name('keys.update');
        Route::delete('/keys/{keyId}', [\App\Http\Controllers\Admin\AdminApiController::class, 'revokeKey'])->name('keys.revoke');
        Route::post('/keys/{keyId}/regenerate', [\App\Http\Controllers\Admin\AdminApiController::class, 'regenerateSecret'])->name('keys.regenerate');
        Route::get('/documentation', [\App\Http\Controllers\Admin\AdminApiController::class, 'documentation'])->name('documentation');
        Route::get('/rate-limits', [\App\Http\Controllers\Admin\AdminApiController::class, 'rateLimits'])->name('rate-limits');
        Route::put('/rate-limits', [\App\Http\Controllers\Admin\AdminApiController::class, 'updateGlobalRateLimits'])->name('rate-limits.update');
        Route::get('/analytics', [\App\Http\Controllers\Admin\AdminApiController::class, 'analytics'])->name('analytics');
    });
    
    Route::prefix('webhooks')->name('webhooks.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminWebhookController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\AdminWebhookController::class, 'store'])->name('store');
        Route::put('/{webhookId}', [\App\Http\Controllers\Admin\AdminWebhookController::class, 'update'])->name('update');
        Route::delete('/{webhookId}', [\App\Http\Controllers\Admin\AdminWebhookController::class, 'destroy'])->name('destroy');
        Route::post('/{webhookId}/test', [\App\Http\Controllers\Admin\AdminWebhookController::class, 'test'])->name('test');
        Route::get('/{webhookId}/logs', [\App\Http\Controllers\Admin\AdminWebhookController::class, 'logs'])->name('logs');
        Route::get('/events', [\App\Http\Controllers\Admin\AdminWebhookController::class, 'events'])->name('events');
    });
    
    Route::prefix('integrations')->name('integrations.')->group(function () {
        Route::get('/', function () { return redirect()->route('dashboard')->with('info', 'Integrations coming soon!'); })->name('index');
    });
    
    Route::prefix('custom-fields')->name('custom-fields.')->group(function () {
        Route::get('/', function () { return redirect()->route('dashboard')->with('info', 'Custom fields coming soon!'); })->name('index');
    });
    
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', function () { return redirect()->route('dashboard')->with('info', 'Notifications management coming soon!'); })->name('index');
        Route::get('send', function () { return redirect()->route('dashboard')->with('info', 'Send notifications coming soon!'); })->name('send');
    });
    
    // Additional admin routes can be added here as controllers are created
    // All sidebar links are prepared and will work once controllers exist
});

// Welcome page (public)
Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

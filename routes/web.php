<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\RoleSwitcherController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

// Admin Controllers (existing ones only)
use App\Http\Controllers\Admin\AdminApiController;
use App\Http\Controllers\Admin\AdminBackupController;
use App\Http\Controllers\Admin\AdminDatabaseController;
use App\Http\Controllers\Admin\AdminDataController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminWebhookController;
use App\Http\Controllers\Admin\TenantSubscriptionController;
use App\Http\Controllers\Admin\TenantAuditLogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Tenant-scoped routes with proper middleware
Route::middleware(['auth', 'tenant.data'])->group(function () {
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');
    
    // Account management with tenant awareness
    Route::resource('accounts', AccountController::class);
    Route::post('/accounts/search', [AccountController::class, 'search'])->name('accounts.search');
    Route::get('/accounts/export/{format?}', [AccountController::class, 'export'])->name('accounts.export');
    
    // Business Management
    Route::resource('customers', CustomerController::class);
    Route::post('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::get('/customers/export/{format?}', [CustomerController::class, 'export'])->name('customers.export');
    
    Route::resource('clients', ClientController::class);
    Route::post('/clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::get('/clients/export/{format?}', [ClientController::class, 'export'])->name('clients.export');
    
    Route::resource('suppliers', SupplierController::class);
    Route::post('/suppliers/search', [SupplierController::class, 'search'])->name('suppliers.search');
    Route::get('/suppliers/export/{format?}', [SupplierController::class, 'export'])->name('suppliers.export');
    
    Route::resource('products', ProductController::class);
    
    // Financial Management
    Route::resource('payments', PaymentController::class);
    
    // Payments export routes
    Route::get('/payments/export/csv', [PaymentController::class, 'exportCsv'])->name('payments.export.csv');
    Route::get('/payments/export/pdf', [PaymentController::class, 'exportPdf'])->name('payments.export.pdf');
    
    Route::resource('incomes', IncomeController::class);
    
    // Incomes export routes
    Route::get('/incomes/export/csv', [IncomeController::class, 'exportCsv'])->name('incomes.export.csv');
    Route::get('/incomes/export/pdf', [IncomeController::class, 'exportPdf'])->name('incomes.export.pdf');
    
    Route::resource('expenses', ExpenseController::class);
    
    // Expenses export routes
    Route::get('/expenses/export/csv', [ExpenseController::class, 'exportCsv'])->name('expenses.export.csv');
    Route::get('/expenses/export/pdf', [ExpenseController::class, 'exportPdf'])->name('expenses.export.pdf');
    
    // Project Management with role-based access
    Route::resource('projects', ProjectController::class);
    
    // Project export routes
    Route::get('/projects/export/csv', [ProjectController::class, 'exportCsv'])->name('projects.export.csv');
    Route::get('/projects/export/pdf', [ProjectController::class, 'exportPdf'])->name('projects.export.pdf');
    
        // Task management routes
    Route::resource('tasks', TaskController::class);
    
    // Task export routes
    Route::get('/tasks/export/csv', [TaskController::class, 'exportCsv'])->name('tasks.export.csv');
    Route::get('/tasks/export/pdf', [TaskController::class, 'exportPdf'])->name('tasks.export.pdf');
    
    // Order management routes  
    Route::resource('orders', OrderController::class);
    
    // Finance management routes
    Route::resource('finance', FinanceController::class);
    
    // Inventory management routes
    Route::resource('inventory', InventoryController::class);
    
    // Invoice management routes
    Route::resource('invoices', InvoiceController::class);
    
    // Role and Permission management routes
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    
    // Human Resources Management
    Route::resource('workers', WorkerController::class);
    
    // Workers export routes
    Route::get('/workers/export/csv', [WorkerController::class, 'exportCsv'])->name('workers.export.csv');
    Route::get('/workers/export/pdf', [WorkerController::class, 'exportPdf'])->name('workers.export.pdf');
    
    // Workers bulk payments route
    Route::post('/workers/payments/bulk', [WorkerController::class, 'bulkStorePayments'])->name('workers.payments.bulk');
    
    Route::resource('employees', EmployeeController::class);
    
    // User management within tenant
    Route::resource('users', UserController::class);
    
    // Reports with tenant isolation
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::get('/reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
    Route::get('/reports/tenant', [ReportController::class, 'tenant'])->name('reports.tenant');
    
    // Report export routes
    Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    
    // Tenant settings (placeholder for future implementation)
    Route::get('/settings', function() { return view('settings.index'); })->name('settings.index');
    Route::post('/settings', function() { return back()->with('success', 'Settings updated successfully.'); })->name('settings.update');
    
    // Role switching functionality
    Route::post('/switch-role/{role}', [RoleSwitcherController::class, 'switch'])->name('role.switch');
    Route::post('/clear-role', [RoleSwitcherController::class, 'clear'])->name('role.clear');
});

// Super Admin routes - requires super admin role
Route::middleware(['auth', 'role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard & Analytics (placeholder)
    Route::get('/', function() { return view('admin.dashboard'); })->name('dashboard');
    Route::get('/dashboard', function() { return view('admin.dashboard'); })->name('dashboard.index');
    Route::get('/analytics', function() { return view('admin.analytics'); })->name('analytics');
    Route::get('/stats', function() { return view('admin.stats'); })->name('stats');
    
    // Tenant Management
    Route::resource('tenants', TenantController::class);
    
    // Tenant Subscription Management
    Route::resource('tenant-subscriptions', TenantSubscriptionController::class);
    Route::post('tenant-subscriptions/{subscription}/activate', [TenantSubscriptionController::class, 'activate'])->name('tenant-subscriptions.activate');
    Route::post('tenant-subscriptions/{subscription}/suspend', [TenantSubscriptionController::class, 'suspend'])->name('tenant-subscriptions.suspend');
    Route::post('tenant-subscriptions/{subscription}/upgrade', [TenantSubscriptionController::class, 'upgrade'])->name('tenant-subscriptions.upgrade');
    Route::post('tenant-subscriptions/{subscription}/downgrade', [TenantSubscriptionController::class, 'downgrade'])->name('tenant-subscriptions.downgrade');
    
    // System Settings
    Route::resource('settings', AdminSettingsController::class);
    Route::post('settings/mail-test', [AdminSettingsController::class, 'testMail'])->name('settings.mail-test');
    Route::post('settings/cache-clear', [AdminSettingsController::class, 'clearCache'])->name('settings.cache-clear');
    Route::post('settings/maintenance-mode', [AdminSettingsController::class, 'maintenanceMode'])->name('settings.maintenance-mode');
    Route::get('settings/phpinfo', [AdminSettingsController::class, 'phpinfo'])->name('settings.phpinfo');
    
    // System Logs
    Route::resource('logs', AdminLogController::class);
    Route::get('logs/system', [AdminLogController::class, 'system'])->name('logs.system');
    Route::get('logs/error', [AdminLogController::class, 'error'])->name('logs.error');
    Route::get('logs/access', [AdminLogController::class, 'access'])->name('logs.access');
    Route::post('logs/clear', [AdminLogController::class, 'clear'])->name('logs.clear');
    Route::get('logs/download/{type}', [AdminLogController::class, 'download'])->name('logs.download');
    
    // Audit Logs
    Route::resource('audit-logs', TenantAuditLogController::class);
    Route::get('audit-logs/tenant/{tenant}', [TenantAuditLogController::class, 'byTenant'])->name('audit-logs.by-tenant');
    Route::get('audit-logs/user/{user}', [TenantAuditLogController::class, 'byUser'])->name('audit-logs.by-user');
    Route::post('audit-logs/export', [TenantAuditLogController::class, 'export'])->name('audit-logs.export');
    
    // Data Management
    Route::resource('data', AdminDataController::class);
    Route::post('data/import', [AdminDataController::class, 'import'])->name('data.import');
    Route::get('data/export/{type}', [AdminDataController::class, 'export'])->name('data.export');
    Route::post('data/cleanup', [AdminDataController::class, 'cleanup'])->name('data.cleanup');
    Route::get('data/statistics', [AdminDataController::class, 'statistics'])->name('data.statistics');
    
    // Backup Management
    Route::resource('backups', AdminBackupController::class);
    Route::post('backups/{backup}/restore', [AdminBackupController::class, 'restore'])->name('backups.restore');
    Route::get('backups/{backup}/download', [AdminBackupController::class, 'download'])->name('backups.download');
    Route::post('backups/schedule', [AdminBackupController::class, 'schedule'])->name('backups.schedule');
    
    // Database Management
    Route::resource('database', AdminDatabaseController::class);
    Route::post('database/query', [AdminDatabaseController::class, 'executeQuery'])->name('database.query');
    Route::get('database/tables', [AdminDatabaseController::class, 'tables'])->name('database.tables');
    Route::get('database/table/{table}', [AdminDatabaseController::class, 'tableDetails'])->name('database.table-details');
    Route::post('database/optimize', [AdminDatabaseController::class, 'optimize'])->name('database.optimize');
    Route::post('database/migrate', [AdminDatabaseController::class, 'migrate'])->name('database.migrate');
    
    // API Management
    Route::resource('api', AdminApiController::class);
    Route::post('api/keys', [AdminApiController::class, 'generateKey'])->name('api.generate-key');
    Route::delete('api/keys/{key}', [AdminApiController::class, 'revokeKey'])->name('api.revoke-key');
    Route::get('api/usage', [AdminApiController::class, 'usage'])->name('api.usage');
    Route::get('api/logs', [AdminApiController::class, 'logs'])->name('api.logs');
    Route::post('api/rate-limits', [AdminApiController::class, 'setRateLimit'])->name('api.rate-limits');
    
    // Webhook Management
    Route::resource('webhooks', AdminWebhookController::class);
    Route::post('webhooks/{webhook}/test', [AdminWebhookController::class, 'test'])->name('webhooks.test');
    Route::get('webhooks/{webhook}/deliveries', [AdminWebhookController::class, 'deliveries'])->name('webhooks.deliveries');
    Route::post('webhooks/{webhook}/deliveries/{delivery}/redeliver', [AdminWebhookController::class, 'redeliver'])->name('webhooks.redeliver');
    Route::get('webhooks/{webhook}/logs', [AdminWebhookController::class, 'logs'])->name('webhooks.logs');
});

require __DIR__.'/auth.php';
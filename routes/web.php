<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\TransactionTypeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;   
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\RoleSwitcherController;
use App\Http\Controllers\WelcomeController;

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
    
    Route::resource('finance', FinanceController::class);
    Route::resource('products', ProductController::class);
    // Product downloads
    Route::get('products/export/csv', [ProductController::class, 'exportCsv'])->name('products.export.csv');
    Route::get('products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
});

// Welcome page (public)
Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

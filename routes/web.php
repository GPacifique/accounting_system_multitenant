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

// Clients resource routes
Route::resource('clients', ClientController::class);

Route::resource('incomes', IncomeController::class);

Route::resource('workers', WorkerController::class);

Route::middleware(['role:admin'])->group(function () {
    Route::resource('users', UserController::class);
});


Route::resource('roles', RoleController::class);


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware(['auth']); // adjust middleware as needed

Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');


Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');


Route::resource('orders', OrderController::class);

// Additional non-resource endpoints
Route::post('orders/{order}/items', [OrderController::class, 'addItem'])->name('orders.items.add');
Route::delete('orders/{order}/items/{item}', [OrderController::class, 'removeItem'])->name('orders.items.remove');
Route::post('orders/{order}/pay', [OrderController::class, 'markAsPaid'])->name('orders.pay');

Route::resource('incomes', IncomeController::class);
Route::resource('users', UserController::class);
Route::resource('employees', EmployeeController::class);
Route::resource('projects', ProjectController::class);
Route::resource('transactions', TransactionController::class);
Route::resource('incomes', IncomeController::class);
Route::resource('finance', FinanceController::class);   
Route::resource('reports', ReportController::class);
Route::resource('expenses', ExpenseController::class);
Route::resource('payments', PaymentController::class);

Route::get('/', function () {
    return view('welcome');
});
Route::resource('permissions', PermissionController::class);


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

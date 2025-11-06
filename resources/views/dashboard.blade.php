{{-- resources/views/dashboard.blade.php --}}
@php $projectStats = $projectStats ?? collect(); @endphp

@extends('layouts.app')
@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('title', 'Financial Dashboard - Construction Project Management & Analytics | SiteLedger')
@section('meta_description', 'Comprehensive construction finance dashboard with real-time analytics. Track project income, expenses, worker payments, and generate detailed financial reports for your construction business.')
@section('meta_keywords', 'construction finance dashboard, project management analytics, construction financial reports, real-time project tracking, construction business intelligence, financial overview')

@php
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$user = Auth::user();
$today = Carbon::today();
$yesterday = $today->copy()->subDay();
$startOfWeek = $today->copy()->startOfWeek();
$endOfWeek = $today->copy()->endOfWeek();
$startOfMonth = $today->copy()->startOfMonth();
$startOfYear = $today->copy()->startOfYear();

// Helper function
$has = function(string $table, ?string $column = null): bool {
    if (!Schema::hasTable($table)) return false;
    return $column ? Schema::hasColumn($table, $column) : true;
};

// TODAY'S STATS
$todayPayments = $has('payments', 'amount') 
    ? DB::table('payments')->whereBetween('created_at', [$today, $today->endOfDay()])->sum('amount') 
    : 0;
$todayExpenses = $has('expenses', 'amount')
    ? DB::table('expenses')->whereBetween('created_at', [$today, $today->endOfDay()])->sum('amount')
    : 0;
$todayIncomes = $has('incomes', 'amount_received')
    ? DB::table('incomes')->whereBetween('received_at', [$today, $today->endOfDay()])->sum('amount_received')
    : 0;

// WEEK'S STATS
$weekPayments = $has('payments', 'amount')
    ? DB::table('payments')->whereBetween('created_at', [$startOfWeek, $endOfWeek->endOfDay()])->sum('amount')
    : 0;
$weekExpenses = $has('expenses', 'amount')
    ? DB::table('expenses')->whereBetween('created_at', [$startOfWeek, $endOfWeek->endOfDay()])->sum('amount')
    : 0;
$weekIncomes = $has('incomes', 'amount_received')
    ? DB::table('incomes')->whereBetween('received_at', [$startOfWeek, $endOfWeek->endOfDay()])->sum('amount_received')
    : 0;

// PAYMENT STATUS
$paidPayments = $has('incomes', 'amount_received')
    ? DB::table('incomes')->sum('amount_received')
    : 0;
$totalPaymentsDue = $has('projects', 'contract_value')
    ? DB::table('projects')->sum('contract_value')
    : 0;
$remainingPayments = max(0, $totalPaymentsDue - $paidPayments);

// PAYMENT TYPES
$advancePayments = $has('incomes') && Schema::hasColumn('incomes', 'type')
    ? DB::table('incomes')->where('type', 'advance')->sum('amount_received')
    : 0;
$regularPayments = $paidPayments - $advancePayments;

// CATEGORY TOTALS
$incomeByCategory = $has('incomes') && Schema::hasColumn('incomes', 'category')
    ? DB::table('incomes')->select('category', DB::raw('SUM(amount_received) as total'))->groupBy('category')->get()
    : collect();

$expenseByCategory = $has('expenses') && Schema::hasColumn('expenses', 'category')
    ? DB::table('expenses')->select('category', DB::raw('SUM(amount) as total'))->groupBy('category')->get()
    : collect();

// Workers
$totalWorkers = $has('workers') ? \App\Models\Worker::count() : 0;
$activeWorkers = $has('workers', 'status')
    ? \App\Models\Worker::where('status','active')->count()
    : $totalWorkers;
$recentWorkers = $has('workers') ? \App\Models\Worker::latest()->limit(6)->get() : collect();

// ENHANCED WORKER STATS
$workersThisMonth = $has('workers')
    ? \App\Models\Worker::whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->count()
    : 0;
$workersTotalSalary = $has('workers', 'salary')
    ? DB::table('workers')->sum('salary')
    : 0;
$workersAvgSalary = $totalWorkers > 0 && $has('workers', 'salary')
    ? round($workersTotalSalary / $totalWorkers, 0)
    : 0;
$workersInactive = $has('workers', 'status')
    ? \App\Models\Worker::where('status', 'inactive')->count()
    : 0;

// EMPLOYEE STATS
$totalEmployees = $has('employees') ? DB::table('employees')->count() : 0;
$employeesThisMonth = $has('employees')
    ? DB::table('employees')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->count()
    : 0;
$employeesTotalSalary = $has('employees', 'salary')
    ? DB::table('employees')->sum('salary')
    : 0;
$employeesAvgSalary = $totalEmployees > 0 && $has('employees', 'salary')
    ? round($employeesTotalSalary / $totalEmployees, 0)
    : 0;

// COMBINED WORKFORCE
$totalWorkforce = $totalWorkers + $totalEmployees;
$totalPayroll = $workersTotalSalary + $employeesTotalSalary;

// ORDER STATS
$totalOrders = $has('orders') ? DB::table('orders')->count() : 0;
$ordersThisMonth = $has('orders')
    ? DB::table('orders')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->count()
    : 0;
$ordersPending = $has('orders', 'status')
    ? DB::table('orders')->where('status', 'pending')->count()
    : 0;
$ordersCompleted = $has('orders', 'status')
    ? DB::table('orders')->where('status', 'completed')->count()
    : 0;
$ordersProcessing = $has('orders', 'status')
    ? DB::table('orders')->where('status', 'processing')->count()
    : 0;
$ordersTotalValue = $has('orders', 'total_amount')
    ? DB::table('orders')->sum('total_amount')
    : 0;

// CLIENT STATS
$totalClients = $has('clients') ? DB::table('clients')->count() : 0;
$clientsThisMonth = $has('clients')
    ? DB::table('clients')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->count()
    : 0;
$activeClients = $has('clients', 'status')
    ? DB::table('clients')->where('status', 'active')->count()
    : $totalClients;

// Payments - all
$paymentsTotal = $has('payments', 'amount')
    ? DB::table('payments')->sum('amount')
    : 0;
$paymentsThisMonth = $has('payments', 'amount')
    ? DB::table('payments')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount')
    : 0;
$recentPayments = $has('payments') ? \App\Models\Payment::latest()->limit(15)->get() : collect();

// Transactions
$recentTransactions = $has('transactions') ? \App\Models\Transaction::latest()->limit(7)->get() : collect();
$transactionsThisMonth = $has('transactions', 'amount')
    ? DB::table('transactions')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount')
    : 0;

// Incomes
$incomesTotal = $has('incomes', 'amount_received')
    ? DB::table('incomes')->sum('amount_received')
    : 0;
$incomesThisMonth = $has('incomes', 'amount_received')
    ? DB::table('incomes')->whereBetween('received_at', [$startOfMonth, $today->endOfDay()])->sum('amount_received')
    : 0;
$recentIncomes = $has('incomes') ? \App\Models\Income::latest()->limit(7)->get() : collect();

// Expenses
$expensesTotal = $has('expenses', 'amount')
    ? DB::table('expenses')->sum('amount')
    : 0;
$expensesThisMonth = $has('expenses', 'amount')
    ? DB::table('expenses')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount')
    : 0;
$recentExpenses = $has('expenses') ? \App\Models\Expense::latest()->limit(7)->get() : collect();

// Projects
$projectsCount = $has('projects') ? DB::table('projects')->count() : 0;
$projectsThisMonth = $has('projects') ? DB::table('projects')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->count() : 0;
$projectsTotal = $has('projects', 'contract_value')
    ? DB::table('projects')->sum('contract_value')
    : null;
$recentProjects = $has('projects') ? \App\Models\Project::latest()->limit(7)->get() : collect();

// Worker Payments (for Accountant dashboard)
$workerPaymentsToday = $has('worker_payments', 'amount') && Schema::hasColumn('worker_payments', 'paid_on')
    ? DB::table('worker_payments')->whereDate('paid_on', $today->toDateString())->sum('amount')
    : 0;
$workerPaymentsThisMonth = $has('worker_payments', 'amount') && Schema::hasColumn('worker_payments', 'paid_on')
    ? DB::table('worker_payments')->whereBetween('paid_on', [$startOfMonth->toDateString(), $today->endOfDay()->toDateString()])->sum('amount')
    : 0;
$recentWorkerPayments = $has('worker_payments') && $has('workers')
    ? DB::table('worker_payments as wp')
        ->join('workers as w', 'w.id', '=', 'wp.worker_id')
        ->orderByDesc('wp.paid_on')
        ->orderByDesc('wp.id')
        ->limit(8)
        ->get(['wp.paid_on', 'wp.amount', 'w.first_name', 'w.last_name', 'w.position'])
    : collect();
$workerPayByPositionMonth = $has('worker_payments') && $has('workers')
    ? DB::table('worker_payments as wp')
        ->join('workers as w', 'w.id', '=', 'wp.worker_id')
        ->whereBetween('wp.paid_on', [$startOfMonth->toDateString(), $today->endOfDay()->toDateString()])
        ->groupBy('w.position')
        ->select('w.position', DB::raw('SUM(wp.amount) as total'))
        ->orderByDesc('total')
        ->get()
    : collect();

// ENHANCED PROJECT STATS
$projectsActive = $has('projects', 'status')
    ? DB::table('projects')->where('status', 'active')->count()
    : $projectsCount;
$projectsCompleted = $has('projects', 'status')
    ? DB::table('projects')->where('status', 'completed')->count()
    : 0;
$projectsPending = $has('projects', 'status')
    ? DB::table('projects')->where('status', 'pending')->count()
    : 0;
$projectsOnHold = $has('projects', 'status')
    ? DB::table('projects')->whereIn('status', ['on_hold', 'paused', 'suspended'])->count()
    : 0;

// PROJECT FINANCIAL STATS
$projectsTotalValue = $has('projects', 'contract_value')
    ? DB::table('projects')->sum('contract_value')
    : 0;
$projectsPaidAmount = $has('incomes', 'amount_received')
    ? DB::table('incomes')->sum('amount_received')
    : 0;
$projectsRemainingAmount = max(0, $projectsTotalValue - $projectsPaidAmount);
$projectsPaymentProgress = $projectsTotalValue > 0 
    ? round(($projectsPaidAmount / $projectsTotalValue) * 100, 1)
    : 0;

// ADVANCE PAYMENT DETAILED STATS
$advancePaymentCount = $has('incomes') && Schema::hasColumn('incomes', 'type')
    ? DB::table('incomes')->where('type', 'advance')->count()
    : 0;
$advancePaymentTotal = $has('incomes') && Schema::hasColumn('incomes', 'type')
    ? DB::table('incomes')->where('type', 'advance')->sum('amount_received')
    : 0;
$advancePaymentThisMonth = $has('incomes') && Schema::hasColumn('incomes', 'type')
    ? DB::table('incomes')->where('type', 'advance')->whereBetween('received_at', [$startOfMonth, $today->endOfDay()])->sum('amount_received')
    : 0;

// FINAL PAYMENT STATS
$finalPaymentTotal = $has('incomes') && Schema::hasColumn('incomes', 'type')
    ? DB::table('incomes')->where('type', 'final')->sum('amount_received')
    : ($paidPayments - $advancePaymentTotal);

// PROJECT COMPLETION RATE
$completionRate = $projectsCount > 0
    ? round(($projectsCompleted / $projectsCount) * 100, 1)
    : 0;

// AVERAGE PROJECT VALUE
$avgProjectValue = $projectsCount > 0
    ? round($projectsTotalValue / $projectsCount, 0)
    : 0;

// THIS MONTH PROJECT STATS
$projectsValueThisMonth = $has('projects', 'contract_value')
    ? DB::table('projects')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('contract_value')
    : 0;

// OUTSTANDING INVOICES (Pending, Overdue, Partially Paid)
if ($has('incomes', 'payment_status') && $has('incomes', 'amount_remaining')) {
    $outstandingBase = DB::table('incomes')->whereIn('payment_status', ['Pending', 'Overdue', 'partially paid']);
    $outstandingInvoices = (clone $outstandingBase)->count();
    $outstandingAmount = (clone $outstandingBase)->sum('amount_remaining');
} else {
    $outstandingInvoices = 0;
    $outstandingAmount = $projectsRemainingAmount; // fallback to project remaining
}

// Monthly series for last 6 months
$months = [];
$paymentsMonthly = [];
$expensesMonthly = [];
$incomeMonthly = [];
for ($i = 5; $i >= 0; $i--) {
    $dt = Carbon::now()->subMonths($i);
    $months[] = $dt->format('M Y');

    $mStart = $dt->copy()->startOfMonth();
    $mEnd = $dt->copy()->endOfMonth();

    $paymentsMonthly[] = $has('payments', 'amount')
        ? DB::table('payments')->whereBetween('created_at', [$mStart, $mEnd])->sum('amount')
        : 0;

    $expensesMonthly[] = $has('expenses', 'amount')
        ? DB::table('expenses')->whereBetween('created_at', [$mStart, $mEnd])->sum('amount')
        : 0;

    $incomeMonthly[] = $has('incomes', 'amount_received')
        ? DB::table('incomes')->whereBetween('received_at', [$mStart, $mEnd])->sum('amount_received')
        : 0;
}
@endphp

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold leading-tight theme-aware-text">Dashboard</h1>
            <p class="text-sm theme-aware-text-muted mt-1">Financial overview, payments & quick actions</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right">
                <div class="text-sm theme-aware-text-muted">Welcome back,</div>
                <div class="font-semibold theme-aware-text">{{ Auth::user()->name }}</div>
                <div class="text-xs px-2 py-1 rounded theme-aware-bg-secondary theme-aware-text-muted">
                    @if(Auth::user()->hasRole('super-admin'))
                        Super Administrator
                    @elseif(Auth::user()->hasRole('admin'))
                        Administrator
                    @elseif(Auth::user()->hasRole('manager'))
                        Manager
                    @elseif(Auth::user()->hasRole('accountant'))
                        Accountant
                    @elseif(Auth::user()->hasRole('employee'))
                        Employee
                    @elseif(Auth::user()->hasRole('client'))
                        Client
                    @elseif(Auth::user()->hasRole('viewer'))
                        Viewer
                    @else
                        User
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- COMPREHENSIVE PROJECT & PAYMENT STATS --}}
    <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 mb-6 theme-aware-border border" style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);">
        <h2 class="text-xl font-bold theme-aware-text mb-4 flex items-center">
            <span class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">📊</span>
            System Overview & Key Metrics
        </h2>

        {{-- Row 1: Project Statistics --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-4">
            <!-- Total Projects -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-blue-500 hover:shadow-md transition">
                <a href="{{ route('projects.index') }}" class="absolute inset-0" aria-label="View Projects"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Total Projects</div>
                <div class="text-3xl font-bold text-blue-600 mt-2">{{ $projectsCount }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">All time</div>
            </div>

            <!-- Active Projects -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-green-500 hover:shadow-md transition">
                <a href="{{ route('projects.index') }}?q=active" class="absolute inset-0" aria-label="Active Projects"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Active Projects</div>
                <div class="text-3xl font-bold text-green-600 mt-2">{{ $projectsActive }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">In progress</div>
            </div>

            <!-- Completed Projects -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-purple-500 hover:shadow-md transition">
                <a href="{{ route('projects.index') }}?q=completed" class="absolute inset-0" aria-label="Completed Projects"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Completed</div>
                <div class="text-3xl font-bold text-purple-600 mt-2">{{ $projectsCompleted }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">{{ $completionRate }}% rate</div>
            </div>

            <!-- Pending Projects -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-yellow-500 hover:shadow-md transition">
                <a href="{{ route('projects.index') }}?q=pending" class="absolute inset-0" aria-label="Pending Projects"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Pending</div>
                <div class="text-3xl font-bold text-yellow-600 mt-2">{{ $projectsPending }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">Not started</div>
            </div>

            <!-- New This Month -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-indigo-500 hover:shadow-md transition">
                <a href="{{ route('projects.index') }}" class="absolute inset-0" aria-label="New Projects"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">New This Month</div>
                <div class="text-3xl font-bold text-indigo-600 mt-2">{{ $projectsThisMonth }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">Projects</div>
            </div>

            <!-- On Hold -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-orange-500 hover:shadow-md transition">
                <a href="{{ route('projects.index') }}?q=hold" class="absolute inset-0" aria-label="On Hold Projects"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">On Hold</div>
                <div class="text-3xl font-bold text-orange-600 mt-2">{{ $projectsOnHold }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">Paused</div>
            </div>
        </div>

        {{-- Row 2: Financial Statistics --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <!-- Total Project Value -->
            <div class="relative theme-aware-bg-card rounded-lg shadow-md p-5 border-l-4 border-emerald-500 hover:shadow-lg transition">
                <a href="{{ route('projects.index') }}" class="absolute inset-0" aria-label="Total Project Value"></a>
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs theme-aware-text-muted font-semibold uppercase">Total Project Value</div>
                    <span class="text-2xl">💎</span>
                </div>
                <div class="text-2xl font-bold text-emerald-600">RWF {{ number_format($projectsTotalValue, 0) }}</div>
                <div class="text-xs theme-aware-text-muted mt-2">Average: RWF {{ number_format($avgProjectValue, 0) }}</div>
                <div class="mt-3 text-xs theme-aware-text-muted">All contracts combined</div>
            </div>

            <!-- Total Paid -->
            <div class="relative theme-aware-bg-card rounded-lg shadow-md p-5 border-l-4 border-green-500 hover:shadow-lg transition">
                <a href="{{ route('incomes.index') }}" class="absolute inset-0" aria-label="Total Paid"></a>
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs theme-aware-text-muted font-semibold uppercase">Total Paid</div>
                    <span class="text-2xl">✅</span>
                </div>
                <div class="text-2xl font-bold text-green-600">RWF {{ number_format($projectsPaidAmount, 0) }}</div>
                <div class="w-full theme-aware-bg-tertiary rounded-full h-2 mt-3">
                    <div class="bg-green-500 h-2 rounded-full transition-all" style="width: {{ $projectsPaymentProgress }}%"></div>
                </div>
                <div class="mt-2 text-xs theme-aware-text-muted">{{ $projectsPaymentProgress }}% of total value</div>
            </div>

            <!-- Remaining Amount -->
            <div class="relative theme-aware-bg-card rounded-lg shadow-md p-5 border-l-4 border-orange-500 hover:shadow-lg transition">
                <a href="{{ route('incomes.index') }}" class="absolute inset-0" aria-label="Remaining Amount"></a>
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs theme-aware-text-muted font-semibold uppercase">Remaining Amount</div>
                    <span class="text-2xl">⏳</span>
                </div>
                <div class="text-2xl font-bold text-orange-600">RWF {{ number_format($projectsRemainingAmount, 0) }}</div>
                <div class="w-full theme-aware-bg-tertiary rounded-full h-2 mt-3">
                    <div class="bg-orange-500 h-2 rounded-full transition-all" style="width: {{ 100 - $projectsPaymentProgress }}%"></div>
                </div>
                <div class="mt-2 text-xs theme-aware-text-muted">{{ round(100 - $projectsPaymentProgress, 1) }}% outstanding</div>
            </div>

            <!-- Outstanding Invoices -->
            <div class="relative theme-aware-bg-card rounded-lg shadow-md p-5 border-l-4 border-red-500 hover:shadow-lg transition">
                <a href="{{ route('incomes.index') }}" class="absolute inset-0" aria-label="Outstanding Invoices"></a>
                <div class="flex items-center justify-between mb-2">
                    <div class="text-xs theme-aware-text-muted font-semibold uppercase">Outstanding Invoices</div>
                    <span class="text-2xl">📄</span>
                </div>
                <div class="text-2xl font-bold text-red-600">{{ $outstandingInvoices }}</div>
                <div class="text-lg font-semibold text-red-500 mt-2">RWF {{ number_format($outstandingAmount, 0) }}</div>
                <div class="mt-2 text-xs theme-aware-text-muted">Pending payment</div>
            </div>
        </div>

        {{-- Row 3: Payment Type Breakdown --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Advance Payments Total -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-t-4 border-cyan-500 hover:shadow-md transition">
                <a href="{{ route('incomes.index') }}?q=advance" class="absolute inset-0" aria-label="Advance Payments"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Advance Payments</div>
                <div class="text-2xl font-bold text-cyan-600 mt-2">RWF {{ number_format($advancePaymentTotal, 0) }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">{{ $advancePaymentCount }} transactions</div>
            </div>

            <!-- Advance This Month -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-t-4 border-teal-500 hover:shadow-md transition">
                <a href="{{ route('incomes.index') }}?q=advance" class="absolute inset-0" aria-label="Advance This Month"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Advance (This Month)</div>
                <div class="text-2xl font-bold text-teal-600 mt-2">RWF {{ number_format($advancePaymentThisMonth, 0) }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">Current month</div>
            </div>

            <!-- Final Payments -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-t-4 border-blue-500 hover:shadow-md transition">
                <a href="{{ route('incomes.index') }}?q=final" class="absolute inset-0" aria-label="Final Payments"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Final Payments</div>
                <div class="text-2xl font-bold text-blue-600 mt-2">RWF {{ number_format($finalPaymentTotal, 0) }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">Completed</div>
            </div>

            <!-- This Month Value -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-t-4 border-violet-500 hover:shadow-md transition">
                <a href="{{ route('projects.index') }}" class="absolute inset-0" aria-label="New Projects Value"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">New Projects Value</div>
                <div class="text-2xl font-bold text-violet-600 mt-2">RWF {{ number_format($projectsValueThisMonth, 0) }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">This month</div>
            </div>

            <!-- Net Cash Flow -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-t-4 border-{{ ($incomesTotal - $expensesTotal) >= 0 ? 'green' : 'red' }}-500 hover:shadow-md transition">
                <a href="{{ route('reports.index') }}" class="absolute inset-0" aria-label="Net Cash Flow"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Net Cash Flow</div>
                <div class="text-2xl font-bold text-{{ ($incomesTotal - $expensesTotal) >= 0 ? 'green' : 'red' }}-600 mt-2">
                    RWF {{ number_format($incomesTotal - $expensesTotal, 0) }}
                </div>
                <div class="text-xs theme-aware-text-muted mt-1">Income - Expenses</div>
            </div>
        </div>
    </div>

    {{-- TODAY & WEEK STATS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 mb-6">
        <!-- Today's Payments -->
        <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border hover:shadow-md transition">
            <a href="{{ route('payments.index') }}" class="absolute inset-0" aria-label="Today's Payments"></a>
            <div class="text-xs theme-aware-text-muted font-medium">TODAY'S PAYMENTS</div>
            <div class="text-2xl font-bold text-blue-600 mt-2">{{ number_format($todayPayments, 0) }}</div>
            <div class="text-xs theme-aware-text-muted mt-1">RWF</div>
        </div>

        <!-- Week's Payments -->
        <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border hover:shadow-md transition">
            <a href="{{ route('payments.index') }}" class="absolute inset-0" aria-label="Week's Payments"></a>
            <div class="text-xs theme-aware-text-muted font-medium">WEEK'S PAYMENTS</div>
            <div class="text-2xl font-bold text-blue-600 mt-2">{{ number_format($weekPayments, 0) }}</div>
            <div class="text-xs theme-aware-text-muted mt-1">RWF</div>
        </div>

        <!-- Today's Expenses -->
        <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border hover:shadow-md transition">
            <a href="{{ route('expenses.index') }}" class="absolute inset-0" aria-label="Today's Expenses"></a>
            <div class="text-xs theme-aware-text-muted font-medium">TODAY'S EXPENSES</div>
            <div class="text-2xl font-bold text-red-600 mt-2">{{ number_format($todayExpenses, 0) }}</div>
            <div class="text-xs theme-aware-text-muted mt-1">RWF</div>
        </div>

        <!-- Week's Expenses -->
        <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border hover:shadow-md transition">
            <a href="{{ route('expenses.index') }}" class="absolute inset-0" aria-label="Week's Expenses"></a>
            <div class="text-xs theme-aware-text-muted font-medium">WEEK'S EXPENSES</div>
            <div class="text-2xl font-bold text-red-600 mt-2">{{ number_format($weekExpenses, 0) }}</div>
            <div class="text-xs theme-aware-text-muted mt-1">RWF</div>
        </div>

        <!-- Remaining Payments -->
        <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border hover:shadow-md transition">
            <a href="{{ route('incomes.index') }}" class="absolute inset-0" aria-label="Remaining Payments"></a>
            <div class="text-xs theme-aware-text-muted font-medium">REMAINING PAYMENTS</div>
            <div class="text-2xl font-bold text-orange-600 mt-2">{{ number_format($remainingPayments, 0) }}</div>
            <div class="text-xs theme-aware-text-muted mt-1">RWF</div>
        </div>

        <!-- Advance Payments -->
        <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border hover:shadow-md transition">
            <a href="{{ route('incomes.index') }}?q=advance" class="absolute inset-0" aria-label="Advance Payments"></a>
            <div class="text-xs theme-aware-text-muted font-medium">ADVANCE PAYMENTS</div>
            <div class="text-2xl font-bold text-green-600 mt-2">{{ number_format($advancePayments, 0) }}</div>
            <div class="text-xs theme-aware-text-muted mt-1">RWF</div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="mb-6 theme-aware-bg-card rounded-lg theme-aware-shadow p-4 theme-aware-border border" style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);">
        <h3 class="font-semibold theme-aware-text mb-3">⚡ Quick Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
            <a href="{{ route('payments.create') ?? '#' }}" class="px-3 py-2 theme-aware-bg-card theme-aware-border border hover:theme-aware-bg-tertiary rounded-lg text-xs font-medium text-center transition theme-aware-text">
                <span class="block">💳 New Payment</span>
            </a>
            <a href="{{ route('incomes.create') ?? '#' }}" class="px-3 py-2 theme-aware-bg-card theme-aware-border border hover:theme-aware-bg-tertiary rounded-lg text-xs font-medium text-center transition theme-aware-text">
                <span class="block">💰 New Income</span>
            </a>
            <a href="{{ route('expenses.create') ?? '#' }}" class="px-3 py-2 theme-aware-bg-card theme-aware-border border hover:theme-aware-bg-tertiary rounded-lg text-xs font-medium text-center transition theme-aware-text">
                <span class="block">📉 New Expense</span>
            </a>
            <a href="{{ route('projects.create') ?? '#' }}" class="px-3 py-2 theme-aware-bg-card theme-aware-border border hover:theme-aware-bg-tertiary rounded-lg text-xs font-medium text-center transition theme-aware-text">
                <span class="block">📋 New Project</span>
            </a>
            <a href="{{ route('payments.index') ?? '#' }}" class="px-3 py-2 theme-aware-bg-card theme-aware-border border hover:theme-aware-bg-tertiary rounded-lg text-xs font-medium text-center transition theme-aware-text">
                <span class="block">📊 View Payments</span>
            </a>
            <a href="{{ route('reports.index') ?? '#' }}" class="px-3 py-2 theme-aware-bg-card theme-aware-border border hover:theme-aware-bg-tertiary rounded-lg text-xs font-medium text-center transition theme-aware-text">
                <span class="block">📈 Reports</span>
            </a>
        </div>
    </div>

    {{-- WORKFORCE & PAYROLL STATS --}}
    @if($totalWorkforce > 0)
    <div class="theme-aware-bg-card rounded-lg theme-aware-shadow p-5 mb-6 theme-aware-border border" style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);">
        <h3 class="font-semibold theme-aware-text mb-4 flex items-center">
            <span class="bg-purple-500 text-white rounded-full w-7 h-7 flex items-center justify-center mr-2 text-sm">👥</span>
            Workforce & Payroll Statistics
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <!-- Total Workforce -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-purple-500 hover:shadow-md transition">
                <a href="{{ route('workers.index') }}" class="absolute inset-0" aria-label="Total Workforce"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Total Workforce</div>
                <div class="text-2xl font-bold text-purple-600 mt-2">{{ $totalWorkforce }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">Workers + Employees</div>
            </div>

            <!-- Active Workers -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-green-500 hover:shadow-md transition">
                <a href="{{ route('workers.index') }}?q=active" class="absolute inset-0" aria-label="Active Workers"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Active Workers</div>
                <div class="text-2xl font-bold text-green-600 mt-2">{{ $activeWorkers }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">of {{ $totalWorkers }} total</div>
            </div>

            <!-- Total Employees -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-blue-500 hover:shadow-md transition">
                <a href="{{ route('employees.index') }}" class="absolute inset-0" aria-label="Employees"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Employees</div>
                <div class="text-2xl font-bold text-blue-600 mt-2">{{ $totalEmployees }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">Staff members</div>
            </div>

            <!-- Monthly Payroll -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-orange-500 hover:shadow-md transition">
                <a href="{{ route('workers.index') }}" class="absolute inset-0" aria-label="Monthly Payroll"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Monthly Payroll</div>
                <div class="text-xl font-bold text-orange-600 mt-2">{{ number_format($totalPayroll, 0) }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">RWF total</div>
            </div>

            <!-- Avg Worker Salary -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-teal-500 hover:shadow-md transition">
                <a href="{{ route('workers.index') }}" class="absolute inset-0" aria-label="Average Worker Pay"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Avg Worker Pay</div>
                <div class="text-xl font-bold text-teal-600 mt-2">{{ number_format($workersAvgSalary, 0) }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">RWF/month</div>
            </div>

            <!-- Avg Employee Salary -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-indigo-500 hover:shadow-md transition">
                <a href="{{ route('employees.index') }}" class="absolute inset-0" aria-label="Average Employee Pay"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Avg Employee Pay</div>
                <div class="text-xl font-bold text-indigo-600 mt-2">{{ number_format($employeesAvgSalary, 0) }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">RWF/month</div>
            </div>
        </div>
    </div>
    @endif

    {{-- ACCOUNTANT & ADMIN: WORKER PAYMENTS SUMMARY --}}
    @if($user->hasAnyRole(['admin', 'accountant']))
    <div class="theme-aware-bg-card rounded-lg theme-aware-shadow p-5 mb-6 theme-aware-border border" style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);">
        <h3 class="font-semibold theme-aware-text mb-4 flex items-center">
            <span class="bg-sky-500 text-white rounded-full w-7 h-7 flex items-center justify-center mr-2 text-sm">💼</span>
            Worker Payments — {{ $user->hasRole('admin') ? 'Admin' : 'Accountant' }} View
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-sky-500 hover:shadow-md transition">
                <a href="{{ route('workers.index') }}" class="absolute inset-0" aria-label="Today's Worker Pay"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Today's Worker Pay</div>
                <div class="text-2xl font-bold text-sky-600 mt-2">RWF {{ number_format($workerPaymentsToday, 0) }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">{{ now()->format('M d, Y') }}</div>
            </div>
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-indigo-500 hover:shadow-md transition">
                <a href="{{ route('workers.index') }}" class="absolute inset-0" aria-label="This Month Worker Pay"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">This Month (Worker Pay)</div>
                <div class="text-2xl font-bold text-indigo-600 mt-2">RWF {{ number_format($workerPaymentsThisMonth, 0) }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">{{ now()->startOfMonth()->format('M d') }} — {{ now()->format('M d') }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Recent Worker Payments -->
            <div class="theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold theme-aware-text text-sm">Recent Worker Payments</h4>
                    <a href="{{ route('workers.index') ?? '#' }}" class="text-xs text-blue-600 hover:text-blue-800">Workers →</a>
                </div>
                @if($recentWorkerPayments->isEmpty())
                    <p class="text-sm theme-aware-text-muted">No worker payments yet</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="theme-aware-bg-secondary theme-aware-border-top border-b">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Date</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Worker</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Position</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($recentWorkerPayments as $wp)
                                    <tr class="hover:theme-aware-bg-secondary transition-colors">
                                        <td class="px-3 py-2 theme-aware-text-secondary">{{ \Carbon\Carbon::parse($wp->paid_on)->format('M d, Y') }}</td>
                                        <td class="px-3 py-2 theme-aware-text">{{ trim(($wp->first_name ?? '').' '.($wp->last_name ?? '')) }}</td>
                                        <td class="px-3 py-2 theme-aware-text-secondary">{{ $wp->position ?? '—' }}</td>
                                        <td class="px-3 py-2 font-semibold text-emerald-600">RWF {{ number_format($wp->amount ?? 0, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Worker Pay by Position (This Month) -->
            <div class="theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border">
                <h4 class="font-semibold theme-aware-text text-sm mb-2">Worker Pay by Position (This Month)</h4>
                @if($workerPayByPositionMonth->isEmpty())
                    <p class="text-sm theme-aware-text-muted">No payments recorded this month</p>
                @else
                    <div class="space-y-2">
                        @php $totalPos = $workerPayByPositionMonth->sum('total'); @endphp
                        @foreach($workerPayByPositionMonth as $row)
                            <div class="flex items-center justify-between">
                                <span class="text-sm theme-aware-text-secondary">{{ $row->position ?? 'Unspecified' }}</span>
                                <span class="text-sm font-semibold text-sky-700">RWF {{ number_format($row->total, 0) }}</span>
                            </div>
                            <div class="w-full theme-aware-bg-tertiary rounded-full h-2">
                                <div class="bg-sky-500 h-2 rounded-full" style="width: {{ $totalPos > 0 ? ($row->total / $totalPos * 100) : 0 }}%"></div>
                            </div>
                        @endforeach
                        <div class="mt-3 pt-3 theme-aware-border-top">
                            <div class="flex items-center justify-between font-semibold">
                                <span>Total</span>
                                <span class="text-sky-700">RWF {{ number_format($totalPos, 0) }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ORDERS & CLIENTS STATS --}}
    @if($totalOrders > 0 || $totalClients > 0)
    <div class="theme-aware-bg-card rounded-lg theme-aware-shadow p-5 mb-6 theme-aware-border border" style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);">
        <h3 class="font-semibold theme-aware-text mb-4 flex items-center">
            <span class="bg-amber-500 text-white rounded-full w-7 h-7 flex items-center justify-center mr-2 text-sm">📦</span>
            Orders & Client Statistics
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <!-- Total Orders -->
            @if($totalOrders > 0)
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-amber-500 hover:shadow-md transition">
                <a href="{{ route('orders.index') }}" class="absolute inset-0" aria-label="Total Orders"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Total Orders</div>
                <div class="text-2xl font-bold text-amber-600 mt-2">{{ $totalOrders }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">All time</div>
            </div>

            <!-- Pending Orders -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-yellow-500 hover:shadow-md transition">
                <a href="{{ route('orders.index') }}?q=pending" class="absolute inset-0" aria-label="Pending Orders"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Pending Orders</div>
                <div class="text-2xl font-bold text-yellow-600 mt-2">{{ $ordersPending }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">Awaiting</div>
            </div>

            <!-- Processing Orders -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-blue-500 hover:shadow-md transition">
                <a href="{{ route('orders.index') }}?q=processing" class="absolute inset-0" aria-label="Processing Orders"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Processing</div>
                <div class="text-2xl font-bold text-blue-600 mt-2">{{ $ordersProcessing }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">In progress</div>
            </div>

            <!-- Completed Orders -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-green-500 hover:shadow-md transition">
                <a href="{{ route('orders.index') }}?q=completed" class="absolute inset-0" aria-label="Completed Orders"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Completed</div>
                <div class="text-2xl font-bold text-green-600 mt-2">{{ $ordersCompleted }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">Done</div>
            </div>

            <!-- Orders This Month -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-purple-500 hover:shadow-md transition">
                <a href="{{ route('orders.index') }}" class="absolute inset-0" aria-label="Orders This Month"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">This Month</div>
                <div class="text-2xl font-bold text-purple-600 mt-2">{{ $ordersThisMonth }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">New orders</div>
            </div>

            <!-- Orders Total Value -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-emerald-500 hover:shadow-md transition">
                <a href="{{ route('orders.index') }}" class="absolute inset-0" aria-label="Orders Total Value"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Orders Value</div>
                <div class="text-xl font-bold text-emerald-600 mt-2">{{ number_format($ordersTotalValue, 0) }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">RWF total</div>
            </div>
            @endif

            <!-- Total Clients -->
            @if($totalClients > 0)
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-cyan-500 hover:shadow-md transition">
                <a href="{{ route('clients.index') }}" class="absolute inset-0" aria-label="Total Clients"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Total Clients</div>
                <div class="text-2xl font-bold text-cyan-600 mt-2">{{ $totalClients }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">All time</div>
            </div>

            <!-- Active Clients -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-teal-500 hover:shadow-md transition">
                <a href="{{ route('clients.index') }}?q=active" class="absolute inset-0" aria-label="Active Clients"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">Active Clients</div>
                <div class="text-2xl font-bold text-teal-600 mt-2">{{ $activeClients }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">Currently active</div>
            </div>

            <!-- New Clients This Month -->
            <div class="relative theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border-l-4 border-indigo-500 hover:shadow-md transition">
                <a href="{{ route('clients.index') }}" class="absolute inset-0" aria-label="New Clients This Month"></a>
                <div class="text-xs theme-aware-text-muted font-medium uppercase">New This Month</div>
                <div class="text-2xl font-bold text-indigo-600 mt-2">{{ $clientsThisMonth }}</div>
                <div class="text-xs theme-aware-text-muted mt-1">New clients</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- CATEGORY BREAKDOWN --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Income by Category -->
        <div class="theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border">
            <h3 class="font-semibold theme-aware-text mb-3">💰 Income by Category</h3>
            @if($incomeByCategory->isEmpty())
                <p class="text-sm theme-aware-text-muted">No income data</p>
            @else
                <div class="space-y-2">
                    @foreach($incomeByCategory as $item)
                        <div class="flex items-center justify-between">
                            <span class="text-sm theme-aware-text-secondary">{{ $item->category ?? 'Uncategorized' }}</span>
                            <span class="text-sm font-semibold text-green-600">RWF {{ number_format($item->total, 0) }}</span>
                        </div>
                        <div class="w-full theme-aware-bg-tertiary rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($item->total / $incomeByCategory->sum('total') * 100) }}%"></div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 pt-3 theme-aware-border-top">
                    <div class="flex items-center justify-between font-semibold">
                        <span>Total Income</span>
                        <span class="text-green-600">RWF {{ number_format($incomeByCategory->sum('total'), 0) }}</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Expense by Category -->
        <div class="theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border">
            <h3 class="font-semibold theme-aware-text mb-3">📉 Expense by Category</h3>
            @if($expenseByCategory->isEmpty())
                <p class="text-sm theme-aware-text-muted">No expense data</p>
            @else
                <div class="space-y-2">
                    @foreach($expenseByCategory as $item)
                        <div class="flex items-center justify-between">
                            <span class="text-sm theme-aware-text-secondary">{{ $item->category ?? 'Uncategorized' }}</span>
                            <span class="text-sm font-semibold text-red-600">RWF {{ number_format($item->total, 0) }}</span>
                        </div>
                        <div class="w-full theme-aware-bg-tertiary rounded-full h-2">
                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ ($item->total / $expenseByCategory->sum('total') * 100) }}%"></div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 pt-3 theme-aware-border-top">
                    <div class="flex items-center justify-between font-semibold">
                        <span>Total Expenses</span>
                        <span class="text-red-600">RWF {{ number_format($expenseByCategory->sum('total'), 0) }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- PAYMENTS TABLE --}}
    <div class="theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold theme-aware-text">📊 Recent Payments</h3>
            <a href="{{ route('payments.index') ?? '#' }}" class="text-xs text-blue-600 hover:text-blue-800">View All →</a>
        </div>

        @if($recentPayments->isEmpty())
            <p class="text-sm theme-aware-text-muted text-center py-4">No payments yet</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="theme-aware-bg-secondary theme-aware-border-top border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Reference</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Method</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Amount</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($recentPayments->take(10) as $payment)
                            <tr class="hover:theme-aware-bg-secondary transition-colors">
                                <td class="px-4 py-2 theme-aware-text-secondary">{{ optional($payment->created_at)->format('M d, Y') }}</td>
                                <td class="px-4 py-2 theme-aware-text-secondary">{{ $payment->reference ?? '—' }}</td>
                                <td class="px-4 py-2 theme-aware-text-secondary">
                                    <span class="text-xs px-2 py-1 rounded theme-aware-bg-secondary">{{ $payment->method ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-2 font-semibold text-green-600">RWF {{ number_format($payment->amount ?? 0, 0) }}</td>
                                <td class="px-4 py-2">
                                    <span class="text-xs px-2 py-1 rounded 
                                        @if(($payment->status ?? '') === 'completed') bg-green-100 text-green-700
                                        @elseif(($payment->status ?? '') === 'pending') bg-yellow-100 text-yellow-700
                                        @else theme-aware-bg-secondary theme-aware-text-secondary @endif">
                                        {{ ucfirst($payment->status ?? '—') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Project Stats Section --}}
    <div class="mb-6 theme-aware-bg-card rounded-lg theme-aware-shadow p-4 theme-aware-border border">
        <h2 class="text-lg font-semibold mb-3 theme-aware-text">📈 Project Payment Summary</h2>

        @php
            $projectStats = collect();
            if ($has('projects') && $has('incomes', 'amount_received')) {
                $projectStats = DB::table('projects')
                    ->leftJoin('incomes', 'projects.id', '=', 'incomes.project_id')
                    ->select(
                        'projects.id',
                        'projects.name as project_name',
                        DB::raw('COALESCE(SUM(incomes.amount_received), 0) as amount_paid'),
                        DB::raw('COALESCE(projects.contract_value, 0) as total_amount'),
                        DB::raw('(COALESCE(projects.contract_value, 0) - COALESCE(SUM(incomes.amount_received), 0)) as amount_remaining')
                    )
                    ->groupBy('projects.id', 'projects.name', 'projects.contract_value')
                    ->limit(8)
                    ->get();
            }
        @endphp

        @if ($projectStats->isEmpty())
            <p class="text-sm theme-aware-text-muted">No project stats available.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm theme-aware-table">
                    <thead class="theme-aware-bg-secondary theme-aware-border-top border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold theme-aware-text-secondary">#</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Project</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Total</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Paid</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Remaining</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y theme-aware-border">
                        @foreach ($projectStats as $index => $proj)
                            @php
                                $progress = $proj->total_amount > 0 ? ($proj->amount_paid / $proj->total_amount * 100) : 0;
                            @endphp
                            <tr class="hover:theme-aware-bg-secondary transition-colors">
                                <td class="px-4 py-2 theme-aware-text-muted">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 font-medium theme-aware-text">{{ $proj->project_name }}</td>
                                <td class="px-4 py-2 theme-aware-text-secondary">RWF {{ number_format($proj->total_amount, 0) }}</td>
                                <td class="px-4 py-2 font-semibold text-green-600">RWF {{ number_format($proj->amount_paid, 0) }}</td>
                                <td class="px-4 py-2 font-semibold text-orange-600">RWF {{ number_format($proj->amount_remaining, 0) }}</td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 theme-aware-bg-secondary rounded-full h-2">
                                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold theme-aware-text-secondary">{{ round($progress) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border min-h-[260px] flex flex-col">
            <h3 class="text-sm font-medium theme-aware-text-secondary mb-2">💰 Income — Last 6 months</h3>
            <div class="flex-1">
                <canvas id="incomeChart" class="w-full h-48"></canvas>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border min-h-[260px] flex flex-col">
            <h3 class="text-sm font-medium theme-aware-text-secondary mb-2">💳 Payments — Last 6 months</h3>
            <div class="flex-1">
                <canvas id="paymentsChart" class="w-full h-48"></canvas>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-lg theme-aware-shadow p-4 border min-h-[260px] flex flex-col">
            <h3 class="text-sm font-medium theme-aware-text-secondary mb-2">📉 Expenses — Last 6 months</h3>
            <div class="flex-1">
                <canvas id="expensesChart" class="w-full h-48"></canvas>
            </div>
        </div>
    </div>

    <!-- Creator Footer -->
    <div class="mt-8 pt-6 theme-aware-border-top border-t text-center">
        <p class="text-sm font-semibold theme-aware-text mb-2">Created by Gashumba</p>
        <p class="text-sm theme-aware-text-secondary mb-3">
            <a href="mailto:gashpaci@gmail.com" class="text-blue-600 hover:text-blue-800 hover:underline">
                <i class="fas fa-envelope me-1"></i> gashpaci@gmail.com
            </a>
        </p>
        <small class="theme-aware-text-muted">© {{ date('Y') }} {{ config('app.name', 'SiteLedger') }}. All rights reserved.</small>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Fine tuning beyond Tailwind utilities */
    .theme-aware-shadow { box-shadow: 0 6px 18px rgba(20,24,40,0.06); }
    .border { border: 1px solid rgba(17,24,39,0.04); }
    .min-h-\[260px\] { min-height: 260px; } /* for older Tailwind compilers */
    /* Small responsive tweaks */
    @media (max-width: 640px) {
        #q { width: 100% !important; }
    }
</style>
@endpush

@push('scripts')
<!-- Chart.js (loaded via CDN) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Chart shorthand
    const months = @json($months);
    const incomeData = @json($incomeMonthly);
    const paymentsData = @json($paymentsMonthly);
    const expensesData = @json($expensesMonthly);

    function createChart(canvasId, label, data, colorFrom, colorTo) {
        const el = document.getElementById(canvasId);
        if (!el) return;
        const ctx = el.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, el.height);
        gradient.addColorStop(0, colorFrom);
        gradient.addColorStop(1, colorTo);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label,
                    data,
                    fill: true,
                    backgroundColor: gradient,
                    borderColor: colorFrom,
                    tension: 0.35,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'white',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: 'rgba(15,23,42,0.04)' } }
                },
                interaction: { mode: 'index', intersect: false }
            }
        });
    }

    createChart('incomeChart', 'Income', incomeData, 'rgba(34,197,94,1)', 'rgba(34,197,94,0.08)');
    createChart('paymentsChart', 'Payments', paymentsData, 'rgba(59,130,246,1)', 'rgba(59,130,246,0.08)');
    createChart('expensesChart', 'Expenses', expensesData, 'rgba(239,68,68,1)', 'rgba(239,68,68,0.06)');

    // Fade-in cards
    document.querySelectorAll('.theme-aware-bg-card.rounded-lg').forEach((el, i) => {
        el.style.opacity = 0;
        el.style.transform = 'translateY(6px)';
        setTimeout(() => {
            el.style.transition = 'opacity .4s ease, transform .4s cubic-bezier(.2,.9,.2,1)';
            el.style.opacity = 1;
            el.style.transform = 'translateY(0)';
        }, 60 * i);
    });
});
</script>
@endpush

{{-- resources/views/dashboard/admin.blade.php --}}
@php 
    use Carbon\Carbon;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    $projectStats = $projectStats ?? collect(); 
@endphp

@extends('layouts.app')

@section('title', 'Admin Dashboard - Complete Financial Overview & Management | SiteLedger')
@section('meta_description', 'Administrative control panel for construction finance management. Access complete system analytics, manage all users, projects, finances, and generate comprehensive business reports.')
@section('meta_keywords', 'admin dashboard, construction management control panel, complete financial overview, system analytics, business administration, construction finance control')


@section('content')
<div class="page-container">
    {{-- Enhanced Page Header --}}
    <x-page-header 
        title="Admin Dashboard" 
        subtitle="System overview, comprehensive financial analysis and key metrics">
        <x-slot name="actions">
            <x-enhanced-button 
                type="success" 
                href="{{ route('projects.create') ?? '#' }}" 
                icon="fas fa-plus">
                New Project
            </x-enhanced-button>
        </x-slot>
    </x-page-header>


    {{-- Quick Stats Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <a href="{{ route('incomes.index') ?? '#' }}" class="block focus:outline-none focus:ring-2 ring-green-300 ring-offset-2 rounded-lg">
        <x-enhanced-card class="stat-card stat-card-hover rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium theme-aware-text-secondary mb-1">Today's Income</p>
                    <p class="text-3xl font-bold theme-aware-text">RWF {{ number_format($quickStats['today_income'], 2) }}</p>
                    <p class="text-sm text-green-600 mt-2">
                        <i class="fas fa-arrow-up mr-1"></i> Received invoices
                    </p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-chart-line text-3xl text-green-600"></i>
                </div>
            </div>
        </x-enhanced-card>
        </a>

        <a href="{{ route('expenses.index') ?? '#' }}" class="block focus:outline-none focus:ring-2 ring-green-300 ring-offset-2 rounded-lg">
        <x-enhanced-card class="stat-card stat-card-hover rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium theme-aware-text-secondary mb-1">Today's Expenses</p>
                    <p class="text-3xl font-bold theme-aware-text">RWF {{ number_format($quickStats['today_expense'], 2) }}</p>
                    <p class="text-sm text-red-600 mt-2">
                        <i class="fas fa-arrow-down mr-1"></i> Recorded expenses
                    </p>
                </div>
                <div class="bg-red-100 rounded-full p-4">
                    <i class="fas fa-money-bill-wave text-3xl text-red-600"></i>
                </div>
            </div>
        </x-enhanced-card>
        </a>

        <a href="{{ route('incomes.index') ?? '#' }}" class="block focus:outline-none focus:ring-2 ring-green-300 ring-offset-2 rounded-lg">
        <x-enhanced-card class="stat-card stat-card-hover rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium theme-aware-text-secondary mb-1">Month Income</p>
                    <p class="text-3xl font-bold theme-aware-text">RWF {{ number_format($quickStats['month_income'], 2) }}</p>
                    <p class="text-sm text-blue-600 mt-2">
                        {{ $financialSummary['this_month']['income'] > 0 ? '✅ On track' : 'No income yet' }}
                    </p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-wallet text-3xl text-blue-600"></i>
                </div>
            </div>
        </x-enhanced-card>
        </a>

        <a href="{{ route('incomes.index') ?? '#' }}" class="block focus:outline-none focus:ring-2 ring-green-300 ring-offset-2 rounded-lg">
        <x-enhanced-card class="stat-card stat-card-hover rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium theme-aware-text-secondary mb-1">Outstanding</p>
                    <p class="text-3xl font-bold theme-aware-text">RWF {{ number_format($quickStats['outstanding'], 2) }}</p>
                    <p class="text-sm text-orange-600 mt-2">
                        <i class="fas fa-clock mr-1"></i> {{ $outstandingReceivables['count'] }} invoices pending
                    </p>
                </div>
                <div class="bg-orange-100 rounded-full p-4">
                    <i class="fas fa-hourglass-half text-3xl text-orange-600"></i>
                </div>
            </div>
        </x-enhanced-card>
        </a>
    </div>


    {{-- Financial Summary Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Today Summary --}}
        <a href="{{ route('reports.index') ?? '#' }}" class="block focus:outline-none focus:ring-2 ring-green-300 ring-offset-2 rounded-lg">
        <x-enhanced-card title="Today" class="border-t-4 border-t-indigo-500">
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm theme-aware-text-secondary">Income</span>
                    <span class="font-semibold text-green-600">RWF {{ number_format($financialSummary['today']['income'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm theme-aware-text-secondary">Expenses</span>
                    <span class="font-semibold text-red-600">RWF {{ number_format($financialSummary['today']['expense'], 2) }}</span>
                </div>
                <div class="border-t theme-aware-border pt-3 flex justify-between items-center">
                    <span class="theme-aware-text-secondary font-medium">Balance</span>
                    <span class="font-bold text-lg {{ $financialSummary['today']['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        RWF {{ number_format($financialSummary['today']['balance'], 2) }}
                    </span>
                </div>
            </div>
        </x-enhanced-card>
        </a>

        {{-- This Month Summary --}}
        <a href="{{ route('reports.index') ?? '#' }}" class="block focus:outline-none focus:ring-2 ring-green-300 ring-offset-2 rounded-lg">
        <x-enhanced-card title="This Month" class="border-t-4 border-t-blue-500">
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm theme-aware-text-secondary">Income</span>
                    <span class="font-semibold text-green-600">RWF {{ number_format($financialSummary['this_month']['income'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm theme-aware-text-secondary">Expenses</span>
                    <span class="font-semibold text-red-600">RWF {{ number_format($financialSummary['this_month']['expense'], 2) }}</span>
                </div>
                <div class="border-t theme-aware-border pt-3 flex justify-between items-center">
                    <span class="theme-aware-text-secondary font-medium">Balance</span>
                    <span class="font-bold text-lg {{ $financialSummary['this_month']['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        RWF {{ number_format($financialSummary['this_month']['balance'], 2) }}
                    </span>
                </div>
            </div>
        </x-enhanced-card>
        </a>

        {{-- This Year Summary --}}
        <a href="{{ route('reports.index') ?? '#' }}" class="block focus:outline-none focus:ring-2 ring-green-300 ring-offset-2 rounded-lg">
        <x-enhanced-card title="This Year" class="border-t-4 border-t-purple-500">
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm theme-aware-text-secondary">Income</span>
                    <span class="font-semibold text-green-600">RWF {{ number_format($financialSummary['this_year']['income'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm theme-aware-text-secondary">Expenses</span>
                    <span class="font-semibold text-red-600">RWF {{ number_format($financialSummary['this_year']['expense'], 2) }}</span>
                </div>
                <div class="border-t theme-aware-border pt-3 flex justify-between items-center">
                    <span class="theme-aware-text-secondary font-medium">Balance</span>
                    <span class="font-bold text-lg {{ $financialSummary['this_year']['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        RWF {{ number_format($financialSummary['this_year']['balance'], 2) }}
                    </span>
                </div>
            </div>
        </x-enhanced-card>
        </a>
    </div>


    {{-- Charts Row 1: Daily and Weekly Trends --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Daily Trend Chart --}}
        <x-dashboard.chart-card
            chartId="dailyTrendChart"
            title="Daily Income vs Expenses"
            subtitle="Last 30 days"
            height="350px"
            :chartData="[
                'labels' => array_map(fn($d) => $d['date_formatted'], $dailyStats),
                'datasets' => [
                    [
                        'label' => 'Income',
                        'data' => array_map(fn($d) => $d['income'], $dailyStats),
                        'borderColor' => '#10b981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'borderWidth' => 2,
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                    [
                        'label' => 'Expense',
                        'data' => array_map(fn($d) => $d['expense'], $dailyStats),
                        'borderColor' => '#ef4444',
                        'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                        'borderWidth' => 2,
                        'fill' => true,
                        'tension' => 0.4,
                    ]
                ]
            ]"
            :chartOptions="[
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => ['position' => 'top'],
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true]
                ]
            ]"
        />

        {{-- Weekly Trend Chart --}}
        <x-dashboard.chart-card
            chartId="weeklyTrendChart"
            title="Weekly Cash Flow"
            subtitle="Last 12 weeks"
            height="350px"
            :chartData="[
                'labels' => array_map(fn($w) => $w['week_label'], $weeklyStats),
                'datasets' => [
                    [
                        'label' => 'Net Cash Flow',
                        'data' => array_map(fn($w) => $w['balance'], $weeklyStats),
                        'backgroundColor' => array_map(fn($w) => $w['balance'] >= 0 ? 'rgba(16, 185, 129, 0.7)' : 'rgba(239, 68, 68, 0.7)', $weeklyStats),
                        'borderRadius' => 4,
                    ]
                ]
            ]"
            :chartOptions="[
                'indexAxis' => 'y',
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => ['legend' => ['display' => false]],
                'scales' => ['x' => ['beginAtZero' => true]]
            ]"
        />
    </div>


    {{-- Charts Row 2: Category Breakdowns --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Income by Category --}}
        <x-dashboard.category-card
            title="Income by Project"
            :items="$incomeByCategory ?? []"
            countLabel="invoices"
            barColor="green"
            :maxValue="!empty($incomeByCategory) ? collect($incomeByCategory)->max('total') ?? 1 : 1"
            emptyMessage="No income recorded yet"
        />

        {{-- Expenses by Category --}}
        <x-dashboard.category-card
            title="Expenses by Category"
            :items="$expenseByCategory ?? []"
            countLabel="transactions"
            barColor="red"
            :maxValue="!empty($expenseByCategory) ? collect($expenseByCategory)->max('total') ?? 1 : 1"
            emptyMessage="No expenses recorded yet"
        />
    </div>


    {{-- Charts Row 3: Payment Status and Methods --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Payment Status Breakdown --}}
        <x-dashboard.chart-card
            chartId="paymentStatusChart"
            title="Payment Status Distribution"
            subtitle="Current invoice status"
            height="300px"
            :chartData="[
                'labels' => array_map(fn($p) => $p['status'], $paymentStatusBreakdown),
                'datasets' => [
                    [
                        'data' => array_map(fn($p) => $p['count'], $paymentStatusBreakdown),
                        'backgroundColor' => [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(249, 115, 22, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(107, 114, 128, 0.8)',
                        ]
                    ]
                ]
            ]"
            :chartOptions="[
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => ['legend' => ['position' => 'bottom']]
            ]"
        />

        {{-- Expenses by Method --}}
        <x-dashboard.category-card
            title="Expenses by Payment Method"
            :items="$expenseByMethod"
            countLabel="transactions"
            barColor="blue"
            :maxValue="!empty($expenseByMethod) ? collect($expenseByMethod)->max('total') ?? 1 : 1"
            emptyMessage="No payment method data"
        />
    </div>


    {{-- Monthly Cash Flow Analysis --}}
    <div class="mb-6">
        <x-dashboard.chart-card
            chartId="cashFlowChart"
            title="Monthly Cash Flow Analysis"
            subtitle="6-month trend with profit margins"
            height="400px"
            :chartData="[
                'labels' => array_map(fn($c) => $c['month_short'], $cashFlowAnalysis),
                'datasets' => [
                    [
                        'label' => 'Income',
                        'data' => array_map(fn($c) => $c['income'], $cashFlowAnalysis),
                        'backgroundColor' => 'rgba(16, 185, 129, 0.7)',
                        'borderColor' => '#10b981',
                        'borderWidth' => 1,
                    ],
                    [
                        'label' => 'Expense',
                        'data' => array_map(fn($c) => $c['expense'], $cashFlowAnalysis),
                        'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
                        'borderColor' => '#ef4444',
                        'borderWidth' => 1,
                    ],
                    [
                        'label' => 'Net Flow',
                        'data' => array_map(fn($c) => $c['net_cash_flow'], $cashFlowAnalysis),
                        'type' => 'line',
                        'borderColor' => '#3b82f6',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'borderWidth' => 3,
                        'fill' => true,
                        'tension' => 0.4,
                    ]
                ]
            ]"
            :chartOptions="[
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => ['legend' => ['position' => 'top']],
                'scales' => ['y' => ['beginAtZero' => true]]
            ]"
        />
    </div>


    {{-- Receivables Alert --}}
    @if($outstandingReceivables['total_outstanding'] > 0)
        <x-enhanced-alert type="warning" class="mb-6">
            <strong>Outstanding Receivables:</strong> RWF {{ number_format($outstandingReceivables['total_outstanding'], 2) }}
            across {{ $outstandingReceivables['count'] }} invoices
            ({{ $outstandingReceivables['pending_count'] }} pending, {{ $outstandingReceivables['overdue_count'] }} overdue)
        </x-enhanced-alert>
    @endif


    {{-- Recent Transactions Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Recent Incomes --}}
        <x-dashboard.transaction-list
            title="Recent Incomes"
            :items="$recentIncomes"
            dateField="received_at"
            amountField="amount_received"
            emptyMessage="No income recorded yet"
        />

        {{-- Recent Expenses --}}
        <x-dashboard.transaction-list
            title="Recent Expenses"
            :items="$recentExpenses"
            dateField="date"
            amountField="amount"
            emptyMessage="No expenses recorded yet"
        />

        {{-- Recent Payments --}}
        <x-dashboard.transaction-list
            title="Recent Payments"
            :items="$recentPayments"
            dateField="created_at"
            amountField="amount"
            emptyMessage="No payments recorded yet"
        />
    </div>

    {{-- Additional Metrics Section --}}
    @php
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        $startOfYear = $today->copy()->startOfYear();

        // Workers
        $totalWorkers = Schema::hasTable('workers') ? \App\Models\Worker::count() : 0;
        $activeWorkers = Schema::hasTable('workers') && Schema::hasColumn('workers', 'status')
            ? \App\Models\Worker::where('status','active')->count()
            : $totalWorkers;
        $recentWorkers = Schema::hasTable('workers') ? \App\Models\Worker::latest()->limit(6)->get() : collect();

        // Payments
        $paymentsTotal = Schema::hasTable('payments') && Schema::hasColumn('payments','amount')
            ? DB::table('payments')->sum('amount')
            : 0;
        $paymentsThisMonth = Schema::hasTable('payments') && Schema::hasColumn('payments','amount')
            ? DB::table('payments')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount')
            : 0;

        // Transactions
        $recentTransactions = Schema::hasTable('transactions') ? \App\Models\Transaction::latest()->limit(7)->get() : collect();
        $transactionsThisMonth = Schema::hasTable('transactions') && Schema::hasColumn('transactions','amount')
            ? DB::table('transactions')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount')
            : 0;

        // Incomes
        $incomesTotal = Schema::hasTable('incomes') && Schema::hasColumn('incomes','amount_received')
            ? DB::table('incomes')->sum('amount_received')
            : 0;
        $incomesThisMonth = Schema::hasTable('incomes') && Schema::hasColumn('incomes','amount_received')
            ? DB::table('incomes')->whereBetween('received_at', [$startOfMonth, $today->endOfDay()])->sum('amount_received')
            : 0;

        // Expenses
        $expensesTotal = Schema::hasTable('expenses') && Schema::hasColumn('expenses','amount')
            ? DB::table('expenses')->sum('amount')
            : 0;
        $expensesThisMonth = Schema::hasTable('expenses') && Schema::hasColumn('expenses','amount')
            ? DB::table('expenses')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount')
            : 0;

        // Projects
        $projectsCount = Schema::hasTable('projects') ? DB::table('projects')->count() : 0;
        $projectsThisMonth = Schema::hasTable('projects') ? DB::table('projects')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->count() : 0;
        $projectsTotal = Schema::hasTable('projects') && Schema::hasColumn('projects','contract_value')
            ? DB::table('projects')->sum('contract_value')
            : null;
        $recentProjects = Schema::hasTable('projects') ? \App\Models\Project::latest()->limit(7)->get() : collect();

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

            $paymentsMonthly[] = Schema::hasTable('payments') && Schema::hasColumn('payments','amount')
                ? DB::table('payments')->whereBetween('created_at', [$mStart, $mEnd])->sum('amount')
                : 0;

            $expensesMonthly[] = Schema::hasTable('expenses') && Schema::hasColumn('expenses','amount')
                ? DB::table('expenses')->whereBetween('created_at', [$mStart, $mEnd])->sum('amount')
                : 0;

            $incomeMonthly[] = Schema::hasTable('incomes') && Schema::hasColumn('incomes','amount_received')
                ? DB::table('incomes')->whereBetween('received_at', [$mStart, $mEnd])->sum('amount_received')
                : 0;
        }
    @endphp

    {{-- Additional KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <a href="{{ route('workers.index') ?? '#' }}" class="block focus:outline-none focus:ring-2 ring-green-300 ring-offset-2 rounded-lg">
        <x-enhanced-card class="stat-card stat-card-hover border-l-4 border-l-indigo-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium theme-aware-text-secondary mb-1">Total Workers</p>
                    <p class="text-3xl font-bold theme-aware-text">{{ number_format($totalWorkers) }}</p>
                    <p class="text-sm text-indigo-600 mt-2">
                        <i class="fas fa-check-circle mr-1"></i> Active: {{ number_format($activeWorkers) }}
                    </p>
                </div>
                <div class="bg-indigo-100 rounded-full p-4">
                    <i class="fas fa-users text-3xl text-indigo-600"></i>
                </div>
            </div>
        </x-enhanced-card>
        </a>

        <a href="{{ route('incomes.index') ?? '#' }}" class="block focus:outline-none focus:ring-2 ring-green-300 ring-offset-2 rounded-lg">
        <x-enhanced-card class="stat-card stat-card-hover border-l-4 border-l-green-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium theme-aware-text-secondary mb-1">Total Incomes</p>
                    <p class="text-3xl font-bold theme-aware-text">{{ number_format($incomesTotal, 2) }}</p>
                    <p class="text-sm text-green-600 mt-2">
                        This month: {{ number_format($incomesThisMonth, 2) }}
                    </p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-dollar-sign text-3xl text-green-600"></i>
                </div>
            </div>
        </x-enhanced-card>
        </a>

        <a href="{{ route('expenses.index') ?? '#' }}" class="block focus:outline-none focus:ring-2 ring-green-300 ring-offset-2 rounded-lg">
        <x-enhanced-card class="stat-card stat-card-hover border-l-4 border-l-red-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium theme-aware-text-secondary mb-1">Total Expenses</p>
                    <p class="text-3xl font-bold theme-aware-text">{{ number_format($expensesTotal, 2) }}</p>
                    <p class="text-sm text-red-600 mt-2">
                        This month: {{ number_format($expensesThisMonth, 2) }}
                    </p>
                </div>
                <div class="bg-red-100 rounded-full p-4">
                    <i class="fas fa-receipt text-3xl text-red-600"></i>
                </div>
            </div>
        </x-enhanced-card>
        </a>

        <a href="{{ route('projects.index') ?? '#' }}" class="block focus:outline-none focus:ring-2 ring-green-300 ring-offset-2 rounded-lg">
        <x-enhanced-card class="stat-card stat-card-hover border-l-4 border-l-yellow-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium theme-aware-text-secondary mb-1">Total Projects</p>
                    <p class="text-3xl font-bold theme-aware-text">{{ number_format($projectsCount) }}</p>
                    <p class="text-sm text-yellow-600 mt-2">
                        This month: {{ number_format($projectsThisMonth) }}
                    </p>
                    @if(!is_null($projectsTotal))
                        <p class="text-xs theme-aware-text-muted mt-1">Budget: {{ number_format($projectsTotal, 2) }}</p>
                    @endif
                </div>
                <div class="bg-yellow-100 rounded-full p-4">
                    <i class="fas fa-project-diagram text-3xl text-yellow-600"></i>
                </div>
            </div>
        </x-enhanced-card>
        </a>
    </div>

    {{-- Daily Category Stats & Project Stats --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Daily totals by category --}}
        <x-enhanced-card title="Daily Totals by Category">
            @if(empty($dailyTotals))
                <p class="text-sm theme-aware-text-muted text-center py-4">No stats available.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="enhanced-table w-full">
                        <thead>
                            <tr>
                                <th class="text-left">Date</th>
                                @foreach($categories as $cat)
                                    <th class="text-left">{{ $cat }}</th>
                                @endforeach
                                <th class="text-left">Daily Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailyTotals as $day => $cats)
                                <tr>
                                    <td class="font-medium">{{ $day }}</td>

                                    @php $rowTotal = 0; @endphp

                                    @foreach($categories as $cat)
                                        @php
                                            $amount = isset($cats[$cat]) ? $cats[$cat] : 0;
                                            $rowTotal += $amount;
                                        @endphp
                                        <td>
                                            @if($amount > 0)
                                                <span class="badge-enhanced badge-enhanced-info">
                                                    {{ number_format($amount, 2) }}
                                                </span>
                                            @else
                                                <span class="theme-aware-text-muted">—</span>
                                            @endif
                                        </td>
                                    @endforeach

                                    <td class="font-semibold text-red-600">
                                        {{ number_format($rowTotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-enhanced-card>

        {{-- Project Payment Summary --}}
        <x-enhanced-card title="Project Payment Summary">
            @if ($projectStats->isEmpty())
                <p class="text-sm theme-aware-text-muted text-center py-4">No project stats available.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="enhanced-table w-full">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-left">Project Name</th>
                                <th class="text-right">Total Amount</th>
                                <th class="text-right">Amount Paid</th>
                                <th class="text-right">Remaining</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projectStats as $index => $proj)
                                <tr>
                                    <td class="text-center theme-aware-text-muted">{{ $index + 1 }}</td>
                                    <td class="font-medium">{{ $proj->project_name }}</td>
                                    <td class="text-right">
                                        <span class="badge-enhanced badge-enhanced-secondary">
                                            {{ number_format($proj->total_amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <span class="badge-enhanced badge-enhanced-success">
                                            {{ number_format($proj->amount_paid, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <span class="badge-enhanced badge-enhanced-danger">
                                            {{ number_format($proj->amount_remaining, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-enhanced-card>
    </div>

    {{-- Charts for 6-month trends --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <x-enhanced-card title="Income — Last 6 Months" class="chart-container-enhanced">
            <canvas id="incomeChart" class="w-full" style="height: 250px;"></canvas>
        </x-enhanced-card>

        <x-enhanced-card title="Expenses — Last 6 Months" class="chart-container-enhanced">
            <canvas id="expensesChart" class="w-full" style="height: 250px;"></canvas>
        </x-enhanced-card>
    </div>

    {{-- Recent lists --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <x-enhanced-card>
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-semibold theme-aware-text-secondary">Recent Employees</h4>
                <a href="{{ route('workers.index') }}" class="text-green-600 text-sm hover:text-green-700">
                    <i class="fas fa-arrow-right ml-1"></i> View all
                </a>
            </div>

            <ul class="divide-y divide-gray-100">
                @forelse($recentWorkers as $work)
                    <li class="py-3 flex items-center justify-between hover:theme-aware-bg-tertiary px-2 rounded">
                        <div>
                            <div class="font-medium theme-aware-text">{{ $work->full_name ?? ($work->name ?? '—') }}</div>
                            <div class="text-xs theme-aware-text-muted">{{ optional($work->created_at)->format('Y-m-d') ?? '—' }}</div>
                        </div>
                        <span class="badge-enhanced {{ ($work->status ?? '') === 'active' ? 'badge-enhanced-success' : 'badge-enhanced-secondary' }}">
                            {{ ucfirst($work->status ?? '—') }}
                        </span>
                    </li>
                @empty
                    <li class="py-8 text-center theme-aware-text-muted">
                        <i class="fas fa-users text-3xl theme-aware-text-muted mb-2"></i>
                        <p>No employees found.</p>
                    </li>
                @endforelse
            </ul>
        </x-enhanced-card>

        <x-enhanced-card>
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-semibold theme-aware-text-secondary">Recent Transactions</h4>
                <a href="{{ route('transactions.index') ?? '#' }}" class="text-green-600 text-sm hover:text-green-700">
                    <i class="fas fa-arrow-right ml-1"></i> View all
                </a>
            </div>

            <ul class="divide-y divide-gray-100">
                @forelse($recentTransactions as $t)
                    <li class="py-3 flex items-center justify-between hover:theme-aware-bg-tertiary px-2 rounded">
                        <div>
                            <div class="font-medium theme-aware-text">{{ $t->type ?? ('#' . ($t->id ?? '—')) }}</div>
                            <div class="text-xs theme-aware-text-muted">
                                {{ isset($t->amount) ? number_format($t->amount,2) : '' }} • 
                                {{ optional($t->created_at)->diffForHumans() ?? '—' }}
                            </div>
                        </div>
                        <span class="text-xs theme-aware-text-muted">{{ $t->status ?? '—' }}</span>
                    </li>
                @empty
                    <li class="py-8 text-center theme-aware-text-muted">
                        <i class="fas fa-exchange-alt text-3xl theme-aware-text-muted mb-2"></i>
                        <p>No transactions found.</p>
                    </li>
                @endforelse
            </ul>
        </x-enhanced-card>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Find all canvas elements with chart data from dashboard components
        document.querySelectorAll('canvas[data-chart]').forEach(canvas => {
            const chartData = JSON.parse(canvas.dataset.chart);
            const chartOptions = JSON.parse(canvas.dataset.options);
            
            new Chart(canvas, {
                type: canvas.id.includes('paymentStatus') ? 'doughnut' : 
                      canvas.id.includes('weekly') ? 'bar' : 'line',
                data: chartData,
                options: chartOptions
            });
        });

        // Chart data for the additional charts
        const months = @json($months);
        const incomeData = @json($incomeMonthly);
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
        createChart('expensesChart', 'Expenses', expensesData, 'rgba(239,68,68,1)', 'rgba(239,68,68,0.06)');
    });
</script>
@endpush

@endsection

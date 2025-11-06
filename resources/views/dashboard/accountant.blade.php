@extends('layouts.app')

@section('title', 'Accountant Dashboard - Revenue, Expenses & Financial Reports | SiteLedger')
@section('meta_description', 'Comprehensive accounting dashboard for construction finance management. Track all revenue streams, monitor expenses, manage accounts receivable, and generate detailed financial reports.')
@section('meta_keywords', 'accountant dashboard, construction accounting, financial reports, revenue tracking, expense management, accounts receivable, construction finance')

@section('content')
<div class="py-12 theme-aware-bg-secondary min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold theme-aware-text">Financial Dashboard</h1>
            <p class="theme-aware-text-secondary mt-2">Comprehensive financial analysis and reporting</p>
        </div>

        {{-- Quick Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-dashboard.stat-card
                title="Today's Income"
                value="{{ 'RWF ' . number_format($quickStats['today_income'], 0) }}"
                icon="📈"
                iconColor="text-green-500"
                borderColor="border-green-500"
                trend="{{ $quickStats['today_income'] > 0 ? 5 : 0 }}"
                trendLabel="last day"
                subtitle="Received invoices"
            />
            
            <x-dashboard.stat-card
                title="Today's Expenses"
                value="{{ 'RWF ' . number_format($quickStats['today_expense'], 0) }}"
                icon="💸"
                iconColor="text-red-500"
                borderColor="border-red-500"
                trend="{{ $quickStats['today_expense'] > 0 ? -3 : 0 }}"
                trendLabel="last day"
                subtitle="Recorded expenses"
            />
            
            <x-dashboard.stat-card
                title="Month Income"
                value="{{ 'RWF ' . number_format($quickStats['month_income'], 0) }}"
                icon="💰"
                iconColor="text-blue-500"
                borderColor="border-blue-500"
                subtitle="{{ $financialSummary['this_month']['income'] > 0 ? '✅ On track' : 'No income yet' }}"
            />
            
            <x-dashboard.stat-card
                title="Outstanding"
                value="{{ 'RWF ' . number_format($quickStats['outstanding'], 0) }}"
                icon="⏰"
                iconColor="text-orange-500"
                borderColor="border-orange-500"
                subtitle="{{ $outstandingReceivables['count'] }} invoices pending"
            />
        </div>

        {{-- Financial Summary Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            {{-- Today Summary --}}
            <div class="theme-aware-bg-card overflow-hidden shadow-lg rounded-lg border-t-4 border-indigo-500">
                <div class="px-6 py-5">
                    <h3 class="text-lg font-semibold theme-aware-text mb-4">Today</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="theme-aware-text-secondary text-sm">Income</span>
                            <span class="font-semibold text-green-600">RWF {{ number_format($financialSummary['today']['income'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="theme-aware-text-secondary text-sm">Expenses</span>
                            <span class="font-semibold text-red-600">RWF {{ number_format($financialSummary['today']['expense'], 2) }}</span>
                        </div>
                        <div class="border-t theme-aware-border pt-3 flex justify-between items-center">
                            <span class="theme-aware-text-secondary font-medium">Balance</span>
                            <span class="font-bold text-lg {{ $financialSummary['today']['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                RWF {{ number_format($financialSummary['today']['balance'], 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- This Month Summary --}}
            <div class="theme-aware-bg-card overflow-hidden shadow-lg rounded-lg border-t-4 border-blue-500">
                <div class="px-6 py-5">
                    <h3 class="text-lg font-semibold theme-aware-text mb-4">This Month</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="theme-aware-text-secondary text-sm">Income</span>
                            <span class="font-semibold text-green-600">RWF {{ number_format($financialSummary['this_month']['income'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="theme-aware-text-secondary text-sm">Expenses</span>
                            <span class="font-semibold text-red-600">RWF {{ number_format($financialSummary['this_month']['expense'], 2) }}</span>
                        </div>
                        <div class="border-t theme-aware-border pt-3 flex justify-between items-center">
                            <span class="theme-aware-text-secondary font-medium">Balance</span>
                            <span class="font-bold text-lg {{ $financialSummary['this_month']['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                RWF {{ number_format($financialSummary['this_month']['balance'], 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- This Year Summary --}}
            <div class="theme-aware-bg-card overflow-hidden shadow-lg rounded-lg border-t-4 border-purple-500">
                <div class="px-6 py-5">
                    <h3 class="text-lg font-semibold theme-aware-text mb-4">This Year</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="theme-aware-text-secondary text-sm">Income</span>
                            <span class="font-semibold text-green-600">RWF {{ number_format($financialSummary['this_year']['income'], 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="theme-aware-text-secondary text-sm">Expenses</span>
                            <span class="font-semibold text-red-600">RWF {{ number_format($financialSummary['this_year']['expense'], 2) }}</span>
                        </div>
                        <div class="border-t theme-aware-border pt-3 flex justify-between items-center">
                            <span class="theme-aware-text-secondary font-medium">Balance</span>
                            <span class="font-bold text-lg {{ $financialSummary['this_year']['balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                RWF {{ number_format($financialSummary['this_year']['balance'], 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row 1: Daily and Weekly Trends --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
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
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
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

        {{-- Charts Row 2b: Transactions by Category --}}
        <div class="grid grid-cols-1 gap-6 mb-8">
            <x-dashboard.category-card
                title="Transactions by Category (This Month)"
                :items="$transactionsByCategory ?? []"
                countLabel="transactions"
                barColor="indigo"
                :maxValue="!empty($transactionsByCategory) ? collect($transactionsByCategory)->max('total') ?? 1 : 1"
                emptyMessage="No transactions recorded yet"
            />
        </div>

        {{-- Charts Row 3: Payment Status and Methods --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
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
                maxValue="{{ $expenseByMethod ? max(array_column($expenseByMethod, 'total')) : 1 }}"
                emptyMessage="No payment method data"
            />
        </div>

        {{-- Monthly Cash Flow Analysis --}}
        <div class="grid grid-cols-1 gap-6 mb-8">
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
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Outstanding Receivables:</strong> RWF {{ number_format($outstandingReceivables['total_outstanding'], 2) }}
                            across {{ $outstandingReceivables['count'] }} invoices
                            ({{ $outstandingReceivables['pending_count'] }} pending, {{ $outstandingReceivables['overdue_count'] }} overdue)
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Recent Transactions Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Find all canvas elements with chart data
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
    });
</script>
@endpush

@endsection


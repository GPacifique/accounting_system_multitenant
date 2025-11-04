@extends('layouts.app')

@section('title', 'Financial Reports - Construction Business Analytics | SiteLedger')
@section('meta_description', 'Comprehensive financial reports and analytics for construction companies. Generate daily, monthly, and custom reports on income, expenses, projects, and overall business performance.')
@section('meta_keywords', 'financial reports, construction analytics, business reports, income reports, expense reports, project reports, construction business intelligence')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold leading-tight theme-aware-text">📊 Financial Reports</h1>
            <p class="text-sm theme-aware-text-muted mt-1">Daily & Monthly financial summary</p>
        </div>
        <div class="flex gap-3">
            {{-- Download Buttons --}}
            <x-download-buttons 
                route="reports.export" 
                filename="financial_reports" 
                size="sm" />
            
            <form method="GET" class="flex gap-2">
                <input type="date" name="date" value="{{ $date }}" class="px-3 py-2 border rounded-lg theme-aware-bg-card theme-aware-text theme-aware-border">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Filter</button>
            </form>
            <a href="{{ route('reports.create') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">+ New Report</a>
        </div>
    </div>

    {{-- Daily Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border hover:shadow-md transition">
            <div class="text-xs theme-aware-text-muted font-medium">TODAY'S INCOME</div>
            <div class="text-2xl font-bold text-green-600 mt-2">RWF {{ number_format($incomeToday, 0) }}</div>
            <div class="text-xs theme-aware-text-muted mt-1">This Month: RWF {{ number_format($incomeThisMonth, 0) }}</div>
        </div>

        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border hover:shadow-md transition">
            <div class="text-xs theme-aware-text-muted font-medium">TODAY'S EXPENSES</div>
            <div class="text-2xl font-bold text-red-600 mt-2">RWF {{ number_format($expensesToday, 0) }}</div>
            <div class="text-xs theme-aware-text-muted mt-1">This Month: RWF {{ number_format($expensesThisMonth, 0) }}</div>
        </div>

        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border hover:shadow-md transition">
            <div class="text-xs theme-aware-text-muted font-medium">TODAY'S PAYMENTS</div>
            <div class="text-2xl font-bold text-blue-600 mt-2">RWF {{ number_format($paymentsToday, 0) }}</div>
            <div class="text-xs theme-aware-text-muted mt-1">This Month: RWF {{ number_format($paymentsThisMonth, 0) }}</div>
        </div>

        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border hover:shadow-md transition">
            <div class="text-xs theme-aware-text-muted font-medium">TODAY'S WORKER PAY</div>
            <div class="text-2xl font-bold text-emerald-600 mt-2">RWF {{ number_format($workerPaymentsToday, 0) }}</div>
            <div class="text-xs theme-aware-text-muted mt-1">This Month: RWF {{ number_format($workerPaymentsThisMonth, 0) }}</div>
        </div>
    </div>

    {{-- Category Breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Income by Category --}}
        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border">
            <h3 class="font-semibold theme-aware-text mb-3">💰 Income by Category</h3>
            @if($incomeByCategory->isEmpty())
                <p class="text-sm theme-aware-text-muted">No income data</p>
            @else
                <div class="space-y-3">
                    @foreach($incomeByCategory as $item)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm theme-aware-text-secondary">{{ $item->category ?? 'Uncategorized' }}</span>
                                <span class="text-sm font-semibold text-green-600">RWF {{ number_format($item->total, 0) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($item->total / $incomeByCategory->sum('total') * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-3 border-t">
                    <div class="flex justify-between font-semibold">
                        <span>Total Income</span>
                        <span class="text-green-600">RWF {{ number_format($incomeByCategory->sum('total'), 0) }}</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- Expense by Category --}}
        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border">
            <h3 class="font-semibold theme-aware-text mb-3">📉 Expense by Category</h3>
            @if($expenseByCategory->isEmpty())
                <p class="text-sm theme-aware-text-muted">No expense data</p>
            @else
                <div class="space-y-3">
                    @foreach($expenseByCategory as $item)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm theme-aware-text-secondary">{{ $item->category ?? 'Uncategorized' }}</span>
                                <span class="text-sm font-semibold text-red-600">RWF {{ number_format($item->total, 0) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-red-500 h-2 rounded-full" style="width: {{ ($item->total / $expenseByCategory->sum('total') * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-3 border-t">
                    <div class="flex justify-between font-semibold">
                        <span>Total Expenses</span>
                        <span class="text-red-600">RWF {{ number_format($expenseByCategory->sum('total'), 0) }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Worker Pay by Position --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border">
            <h3 class="font-semibold theme-aware-text mb-3">👷 Worker Pay by Position (Today)</h3>
            @php $byPosToday = collect($workerPayByPositionToday ?? []); @endphp
            @if($byPosToday->isEmpty())
                <p class="text-sm theme-aware-text-muted">No worker payments recorded for this date.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Position</th>
                                <th class="px-3 py-2 text-right text-xs font-semibold theme-aware-text-secondary">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($byPosToday as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2">{{ $row->position ?? '—' }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-emerald-600">RWF {{ number_format($row->total ?? 0, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border">
            <h3 class="font-semibold theme-aware-text mb-3">📅 Worker Pay by Position (This Month)</h3>
            @php $byPosMonth = collect($workerPayByPositionMonth ?? []); @endphp
            @if($byPosMonth->isEmpty())
                <p class="text-sm theme-aware-text-muted">No worker payments recorded this month.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold theme-aware-text-secondary">Position</th>
                                <th class="px-3 py-2 text-right text-xs font-semibold theme-aware-text-secondary">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($byPosMonth as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2">{{ $row->position ?? '—' }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-emerald-600">RWF {{ number_format($row->total ?? 0, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
        {{-- Recent Incomes --}}
        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border">
            <h3 class="font-semibold theme-aware-text mb-3">💰 Recent Incomes</h3>
            @if($recentIncomes->isEmpty())
                <p class="text-sm theme-aware-text-muted text-center py-4">No incomes for this date</p>
            @else
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($recentIncomes as $income)
                        <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50">
                            <div>
                                <div class="text-sm font-medium text-gray-700">{{ $income->source ?? 'Income' }}</div>
                                <div class="text-xs theme-aware-text-muted">{{ $income->received_at->format('H:i') }}</div>
                            </div>
                            <span class="text-sm font-semibold text-green-600">+RWF {{ number_format($income->amount_received, 0) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Expenses --}}
        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border">
            <h3 class="font-semibold theme-aware-text mb-3">📉 Recent Expenses</h3>
            @if($recentExpenses->isEmpty())
                <p class="text-sm theme-aware-text-muted text-center py-4">No expenses for this date</p>
            @else
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($recentExpenses as $expense)
                        <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50">
                            <div>
                                <div class="text-sm font-medium text-gray-700">{{ $expense->category ?? 'Expense' }}</div>
                                <div class="text-xs theme-aware-text-muted">{{ $expense->created_at->format('H:i') }}</div>
                            </div>
                            <span class="text-sm font-semibold text-red-600">-RWF {{ number_format($expense->amount, 0) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Payments --}}
        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border">
            <h3 class="font-semibold theme-aware-text mb-3">💳 Recent Payments</h3>
            @if($recentPayments->isEmpty())
                <p class="text-sm theme-aware-text-muted text-center py-4">No payments for this date</p>
            @else
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($recentPayments as $payment)
                        <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50">
                            <div>
                                <div class="text-sm font-medium text-gray-700">{{ $payment->reference ?? 'Payment' }}</div>
                                <div class="text-xs theme-aware-text-muted">{{ $payment->method ?? '—' }}</div>
                            </div>
                            <span class="text-sm font-semibold text-blue-600">RWF {{ number_format($payment->amount, 0) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent Worker Payments --}}
        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border">
            <h3 class="font-semibold theme-aware-text mb-3">👷 Daily Worker Payments</h3>
            @if(collect($recentWorkerPayments)->isEmpty())
                <p class="text-sm theme-aware-text-muted text-center py-4">No worker payments for this date</p>
            @else
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($recentWorkerPayments as $wp)
                        <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50">
                            <div>
                                <div class="text-sm font-medium text-gray-700">{{ ($wp->first_name ?? '') . ' ' . ($wp->last_name ?? '') }}</div>
                                <div class="text-xs theme-aware-text-muted">{{ \Illuminate\Support\Carbon::parse($wp->paid_on)->format('Y-m-d') }}</div>
                            </div>
                            <a href="{{ route('workers.show', $wp->worker_id ?? 0) }}" class="text-sm font-semibold text-emerald-600 hover:underline">RWF {{ number_format($wp->amount, 0) }}</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Summary --}}
    <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border">
        <h3 class="font-semibold theme-aware-text mb-4">📈 Summary</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-3 border rounded-lg">
                <div class="text-xs theme-aware-text-muted font-medium">Total Workers</div>
                <div class="text-xl font-bold theme-aware-text mt-1">{{ $totalWorkers }}</div>
            </div>
            <div class="p-3 border rounded-lg">
                <div class="text-xs theme-aware-text-muted font-medium">Projects</div>
                <div class="text-xl font-bold theme-aware-text mt-1">{{ $projectsCount }}</div>
            </div>
            <div class="p-3 border rounded-lg">
                <div class="text-xs theme-aware-text-muted font-medium">This Month</div>
                <div class="text-xl font-bold theme-aware-text mt-1">{{ $projectsThisMonth }}</div>
            </div>
            <div class="p-3 border rounded-lg">
                <div class="text-xs theme-aware-text-muted font-medium">Net Flow</div>
                @php
                    $net = ($incomeThisMonth + $paymentsThisMonth) - $expensesThisMonth;
                @endphp
                <div class="text-xl font-bold {{ $net >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                    RWF {{ number_format($net, 0) }}
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 text-sm theme-aware-text-muted">© {{ date('Y') }} {{ config('app.name', 'SiteLedger') }}</div>
</div>
@endsection

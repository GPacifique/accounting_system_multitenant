@extends('layouts.app')

@section('title', 'Gym Financial Dashboard')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">💰 Gym Financial Dashboard</h1>
            <p class="text-muted">Financial overview and revenue management</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('gym-revenues.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Add Revenue
            </a>
            <a href="{{ route('expenses.create') }}" class="btn btn-outline-danger">
                <i class="fas fa-minus"></i> Add Expense
            </a>
            <a href="{{ route('financial-reports') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-line"></i> Reports
            </a>
        </div>
    </div>

    <!-- Financial KPIs Row -->
    <div class="row mb-4">
        <!-- Total Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ currency($financialSummary['total_revenue'] ?? 0) }}
                            </div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up"></i> 
                                {{ $financialSummary['revenue_growth'] ?? 0 }}% growth
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Expenses
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ currency($financialSummary['total_expenses'] ?? 0) }}
                            </div>
                            <div class="text-xs text-danger">
                                <i class="fas fa-arrow-up"></i> 
                                {{ $financialSummary['expense_growth'] ?? 0 }}% growth
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Net Profit
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ currency($financialSummary['net_profit'] ?? 0) }}
                            </div>
                            <div class="text-xs {{ ($financialSummary['profit_margin'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-percentage"></i> 
                                {{ $financialSummary['profit_margin'] ?? 0 }}% margin
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                This Month
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ currency($financialSummary['revenue_this_month'] ?? 0) }}
                            </div>
                            <div class="text-xs text-info">
                                <i class="fas fa-calendar"></i> 
                                Monthly target
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue vs Expenses Trend -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue vs Expenses Trend (12 Months)</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Export Options:</div>
                            <a class="dropdown-item" href="#">Download PNG</a>
                            <a class="dropdown-item" href="#">Download PDF</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="financialTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue by Category -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue by Category</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="revenueCategoryChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @if(!empty($revenueByCategory))
                            @foreach($revenueByCategory as $category => $amount)
                            <span class="mr-2">
                                <i class="fas fa-circle" style="color: {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'][array_search($category, array_keys($revenueByCategory)) % 5] }}"></i>
                                {{ ucfirst(str_replace('_', ' ', $category)) }}
                            </span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses and Payment Methods Row -->
    <div class="row mb-4">
        <!-- Expenses by Category -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Expenses by Category</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="expenseCategoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue by Payment Method</h6>
                </div>
                <div class="card-body">
                    <div class="chart-doughnut">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Row -->
    <div class="row">
        <!-- Recent Revenues -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Revenues</h6>
                    <a href="{{ route('gym-revenues.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentRevenues as $revenue)
                                <tr>
                                    <td class="text-muted small">
                                        {{ \Carbon\Carbon::parse($revenue->received_at)->format('M j, Y') }}
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ ucfirst(str_replace('_', ' ', $revenue->revenue_type)) }}</div>
                                        <div class="text-muted small">{{ Str::limit($revenue->description, 30) }}</div>
                                    </td>
                                    <td class="text-success font-weight-bold">
                                        +{{ currency($revenue->amount) }}
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ ucfirst($revenue->payment_method ?? 'N/A') }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-dollar-sign fa-3x mb-3"></i>
                                        <p>No recent revenues</p>
                                        <a href="{{ route('gym-revenues.create') }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-plus"></i> Add Revenue
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Expenses</h6>
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentExpenses as $expense)
                                <tr>
                                    <td class="text-muted small">
                                        {{ \Carbon\Carbon::parse($expense->date)->format('M j, Y') }}
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ ucfirst($expense->category) }}</div>
                                        <div class="text-muted small">{{ Str::limit($expense->description, 30) }}</div>
                                    </td>
                                    <td class="text-danger font-weight-bold">
                                        -{{ currency($expense->amount) }}
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $expense->status === 'paid' ? 'success' : 'warning' }}">
                                            {{ ucfirst($expense->status ?? 'Paid') }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-receipt fa-3x mb-3"></i>
                                        <p>No recent expenses</p>
                                        <a href="{{ route('expenses.create') }}" class="btn btn-danger btn-sm">
                                            <i class="fas fa-plus"></i> Add Expense
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary Row -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Financial Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="h4 font-weight-bold text-success">{{ currency($financialSummary['revenue_this_month'] ?? 0) }}</div>
                            <div class="text-xs text-uppercase text-muted">Revenue This Month</div>
                        </div>
                        <div class="col-md-2">
                            <div class="h4 font-weight-bold text-danger">{{ currency($financialSummary['expenses_this_month'] ?? 0) }}</div>
                            <div class="text-xs text-uppercase text-muted">Expenses This Month</div>
                        </div>
                        <div class="col-md-2">
                            <div class="h4 font-weight-bold text-primary">{{ currency($financialSummary['profit_this_month'] ?? 0) }}</div>
                            <div class="text-xs text-uppercase text-muted">Profit This Month</div>
                        </div>
                        <div class="col-md-2">
                            <div class="h4 font-weight-bold text-info">{{ number_format($financialSummary['profit_margin'] ?? 0, 1) }}%</div>
                            <div class="text-xs text-uppercase text-muted">Profit Margin</div>
                        </div>
                        <div class="col-md-2">
                            <div class="h4 font-weight-bold text-warning">{{ number_format($financialSummary['revenue_growth'] ?? 0, 1) }}%</div>
                            <div class="text-xs text-uppercase text-muted">Revenue Growth</div>
                        </div>
                        <div class="col-md-2">
                            <div class="h4 font-weight-bold text-secondary">{{ number_format(($financialSummary['revenue_this_month'] ?? 0) / max(date('j'), 1), 0) }}</div>
                            <div class="text-xs text-uppercase text-muted">Avg Daily Revenue</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Financial Trend Chart
    const financialTrendCtx = document.getElementById('financialTrendChart').getContext('2d');
    new Chart(financialTrendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($monthlyRevenue) !!},
                borderColor: 'rgba(28, 200, 138, 1)',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.3,
                fill: true
            }, {
                label: 'Expenses',
                data: {!! json_encode($monthlyExpenses) !!},
                borderColor: 'rgba(231, 74, 59, 1)',
                backgroundColor: 'rgba(231, 74, 59, 0.1)',
                tension: 0.3,
                fill: true
            }, {
                label: 'Profit',
                data: {!! json_encode($monthlyProfit) !!},
                borderColor: 'rgba(78, 115, 223, 1)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'RWF ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Revenue by Category Chart
    @if(!empty($revenueByCategory))
    const revenueCategoryCtx = document.getElementById('revenueCategoryChart').getContext('2d');
    new Chart(revenueCategoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_map(function($key) { return ucfirst(str_replace('_', ' ', $key)); }, array_keys($revenueByCategory))) !!},
            datasets: [{
                data: {!! json_encode(array_values($revenueByCategory)) !!},
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    @endif

    // Expense Category Chart
    @if(!empty($expensesByCategory))
    const expenseCategoryCtx = document.getElementById('expenseCategoryChart').getContext('2d');
    new Chart(expenseCategoryCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_map('ucfirst', array_keys($expensesByCategory))) !!},
            datasets: [{
                label: 'Amount',
                data: {!! json_encode(array_values($expensesByCategory)) !!},
                backgroundColor: 'rgba(231, 74, 59, 0.8)',
                borderColor: 'rgba(231, 74, 59, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'RWF ' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    @endif

    // Payment Method Chart
    @if(!empty($revenueByPaymentMethod))
    const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
    new Chart(paymentMethodCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_map('ucfirst', array_keys($revenueByPaymentMethod))) !!},
            datasets: [{
                data: {!! json_encode(array_values($revenueByPaymentMethod)) !!},
                backgroundColor: ['#36b9cc', '#1cc88a', '#f6c23e', '#e74a3b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    @endif
});
</script>
@endpush

@push('styles')
<style>
.chart-area {
    position: relative;
    height: 20rem;
    width: 100%;
}

.chart-pie {
    position: relative;
    height: 15rem;
    width: 100%;
}

.chart-bar, .chart-doughnut {
    position: relative;
    height: 12rem;
    width: 100%;
}
</style>
@endpush
@endsection
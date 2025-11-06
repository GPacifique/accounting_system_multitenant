@extends('layouts.app')

@section('title', 'Advanced Analytics - Financial Dashboard | SiteLedger')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Advanced Analytics</h1>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <!-- Financial Summary Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Revenue</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($financialSummary['total_income'] ?? 0) }} RWF
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Net Profit</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($financialSummary['net_profit'] ?? 0) }} RWF
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Total Expenses</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($financialSummary['total_expenses'] ?? 0) }} RWF
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Outstanding</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($outstandingReceivables['total'] ?? 0) }} RWF
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <!-- Daily Trends Chart -->
                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Revenue & Expense Trends (30 Days)</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="dailyTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Income by Category Pie Chart -->
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Income by Category</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="incomeCategoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Analytics Row -->
            <div class="row">
                <!-- Expense Categories -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Expense Categories</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-bar">
                                <canvas id="expenseCategoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Projects -->
                <div class="col-xl-6 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Top Performing Projects</h6>
                        </div>
                        <div class="card-body">
                            @if(!empty($topProjects) && count($topProjects) > 0)
                                @foreach($topProjects as $project)
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="mb-0">{{ $project['name'] ?? 'Project' }}</h6>
                                            <small class="text-muted">{{ $project['status'] ?? 'Active' }}</small>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-weight-bold">{{ number_format($project['total_income'] ?? 0) }} RWF</div>
                                            <small class="text-success">{{ number_format($project['profit'] ?? 0) }} RWF profit</small>
                                        </div>
                                    </div>
                                    <div class="progress mb-3">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $project['progress'] ?? 0 }}%" 
                                             aria-valuenow="{{ $project['progress'] ?? 0 }}" 
                                             aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No project data available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cash Flow Analysis -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Cash Flow Analysis (6 Months)</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="cashFlowChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Daily Trends Chart
const dailyCtx = document.getElementById('dailyTrendsChart').getContext('2d');
const dailyChart = new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(collect($dailyStats)->pluck('date')->toArray()) !!},
        datasets: [{
            label: 'Income',
            data: {!! json_encode(collect($dailyStats)->pluck('income')->toArray()) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }, {
            label: 'Expenses',
            data: {!! json_encode(collect($dailyStats)->pluck('expenses')->toArray()) !!},
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Income Category Chart
const incomeCategoryCtx = document.getElementById('incomeCategoryChart').getContext('2d');
const incomeCategoryChart = new Chart(incomeCategoryCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($incomeByCategory ?? [])) !!},
        datasets: [{
            data: {!! json_encode(array_values($incomeByCategory ?? [])) !!},
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Expense Category Chart
const expenseCategoryCtx = document.getElementById('expenseCategoryChart').getContext('2d');
const expenseCategoryChart = new Chart(expenseCategoryCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($expenseByCategory ?? [])) !!},
        datasets: [{
            label: 'Expenses',
            data: {!! json_encode(array_values($expenseByCategory ?? [])) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Cash Flow Chart
const cashFlowCtx = document.getElementById('cashFlowChart').getContext('2d');
const cashFlowChart = new Chart(cashFlowCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(collect($cashFlowAnalysis)->pluck('month')->toArray()) !!},
        datasets: [{
            label: 'Net Cash Flow',
            data: {!! json_encode(collect($cashFlowAnalysis)->pluck('net_flow')->toArray()) !!},
            backgroundColor: function(context) {
                const value = context.parsed.y;
                return value >= 0 ? 'rgba(75, 192, 192, 0.8)' : 'rgba(255, 99, 132, 0.8)';
            },
            borderColor: function(context) {
                const value = context.parsed.y;
                return value >= 0 ? 'rgba(75, 192, 192, 1)' : 'rgba(255, 99, 132, 1)';
            },
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush
@endsection
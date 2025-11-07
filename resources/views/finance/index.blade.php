@extends('layouts.app')

@section('title', 'Finance Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 theme-aware-text">Finance Dashboard</h1>
            <p class="text-muted">Comprehensive financial overview and management</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-line me-1"></i> Financial Reports
            </a>
            <a href="{{ route('incomes.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Add Income
            </a>
            <a href="{{ route('expenses.create') }}" class="btn btn-danger">
                <i class="fas fa-plus me-1"></i> Add Expense
            </a>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Income (Monthly)
                            </div>
                            <div class="h5 mb-0 font-weight-bold theme-aware-text">
                                {{ currency(0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Expenses (Monthly)
                            </div>
                            <div class="h5 mb-0 font-weight-bold theme-aware-text">
                                {{ currency(0) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
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
                                Net Profit (Monthly)
                            </div>
                            <div class="h5 mb-0 font-weight-bold theme-aware-text">
                                {{ currency(0) }}
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Outstanding Payments
                            </div>
                            <div class="h5 mb-0 font-weight-bold theme-aware-text">
                                {{ currency(0) }}
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

    <!-- Quick Access Section -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Income</h6>
                    <a href="{{ route('incomes.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-money-bill-wave fa-3x mb-3"></i>
                        <p>No recent income records found.</p>
                        <a href="{{ route('incomes.create') }}" class="btn btn-success">Add First Income</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Expenses</h6>
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-receipt fa-3x mb-3"></i>
                        <p>No recent expense records found.</p>
                        <a href="{{ route('expenses.create') }}" class="btn btn-danger">Add First Expense</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Financial Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <a href="{{ route('incomes.index') }}" class="btn btn-outline-success btn-lg d-block">
                                    <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                    <br>Manage Income
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <a href="{{ route('expenses.index') }}" class="btn btn-outline-danger btn-lg d-block">
                                    <i class="fas fa-receipt fa-2x mb-2"></i>
                                    <br>Manage Expenses
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <a href="{{ route('payments.index') }}" class="btn btn-outline-warning btn-lg d-block">
                                    <i class="fas fa-credit-card fa-2x mb-2"></i>
                                    <br>Payments
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <a href="{{ route('reports.index') }}" class="btn btn-outline-info btn-lg d-block">
                                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                    <br>Financial Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endpush
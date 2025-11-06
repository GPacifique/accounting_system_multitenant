@extends('layouts.app')

@section('title', 'Financial Transactions')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Financial Transactions</h1>
            <p class="text-muted">All financial transactions across the system</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('finance.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Finance
            </a>
        </div>
    </div>

    <!-- Transaction Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Income Transactions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Expense Transactions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Payment Transactions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Categories -->
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">Income Transactions</h6>
                    <a href="{{ route('incomes.index') }}" class="btn btn-sm btn-success">Manage</a>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-money-bill-wave fa-3x text-success mb-3"></i>
                    <h5>Project Payments</h5>
                    <p class="text-muted">Track all incoming payments from clients and projects.</p>
                    <a href="{{ route('incomes.create') }}" class="btn btn-outline-success">Add Income</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger">Expense Transactions</h6>
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-danger">Manage</a>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-receipt fa-3x text-danger mb-3"></i>
                    <h5>Business Expenses</h5>
                    <p class="text-muted">Track all business expenses, from office supplies to project costs.</p>
                    <a href="{{ route('expenses.create') }}" class="btn btn-outline-danger">Add Expense</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-warning">Payment Transactions</h6>
                    <a href="{{ route('payments.index') }}" class="btn btn-sm btn-warning">Manage</a>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-credit-card fa-3x text-warning mb-3"></i>
                    <h5>Employee Payments</h5>
                    <p class="text-muted">Process salary payments and track employee compensation.</p>
                    <a href="{{ route('payments.create') }}" class="btn btn-outline-warning">Process Payment</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Quick Navigation</h6>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3 mb-3">
                    <a href="{{ route('accounts.index') }}" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-list-alt fa-2x mb-2"></i>
                        <br>Chart of Accounts
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('projects.index') }}" class="btn btn-outline-info btn-block">
                        <i class="fas fa-project-diagram fa-2x mb-2"></i>
                        <br>Project Finance
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('workers.index') }}" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <br>Worker Payments
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-dark btn-block">
                        <i class="fas fa-chart-bar fa-2x mb-2"></i>
                        <br>Reports
                    </a>
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
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endpush
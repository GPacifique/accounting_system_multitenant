@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-warning text-dark text-center">
            <h3>Accountant Dashboard</h3>
        </div>
        <div class="card-body text-center">
            <h5>Welcome, Accountant!</h5>
            <p class="text-muted">Manage revenues, expenses, and generate financial reports for the company.</p>

            <div class="row mt-4">
                <!-- Revenues -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Revenues</h5>
                            <p class="card-text">Track payments from clients and project income.</p>
                            <a href="#" class="btn btn-outline-warning">View Revenues</a>
                        </div>
                    </div>
                </div>

                <!-- Expenses -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Expenses</h5>
                            <p class="card-text">Monitor vendor payments and operational costs.</p>
                            <a href="#" class="btn btn-outline-warning">View Expenses</a>
                        </div>
                    </div>
                </div>

                <!-- Financial Reports -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Financial Reports</h5>
                            <p class="card-text">Generate detailed profit & loss statements.</p>
                            <a href="#" class="btn btn-outline-warning">Generate Reports</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ url('/') }}" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</div>
@endsection

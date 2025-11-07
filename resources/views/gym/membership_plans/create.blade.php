@extends('layouts.app')

@section('title', 'Create Membership Plan - GymPro')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-id-card text-primary me-2"></i>
            Create Membership Plan
        </h1>
        <a href="{{ route('gym.membership-plans.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('gym.membership-plans.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Plan Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="duration_days" class="form-label">Duration (days)</label>
                        <input type="number" id="duration_days" name="duration_days" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" step="0.01" id="price" name="price" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="payment_frequency" class="form-label">Payment Frequency</label>
                        <select id="payment_frequency" name="payment_frequency" class="form-select">
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="annual">Annual</option>
                            <option value="one_time">One-time</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="benefits" class="form-label">Benefits (comma separated)</label>
                    <textarea id="benefits" name="benefits" class="form-control" rows="3"></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('gym.membership-plans.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

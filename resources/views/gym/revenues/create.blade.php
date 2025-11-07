@extends('layouts.app')

@section('title', 'Add Gym Revenue - GymPro')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cash-register text-primary me-2"></i>
            Add Gym Revenue
        </h1>
        <a href="{{ route('gym.gym-revenues.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Back to Revenues
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Record New Revenue</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('gym.gym-revenues.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="transaction_date" class="form-label">Date</label>
                            <input type="date" id="transaction_date" name="transaction_date" class="form-control" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" step="0.01" id="amount" name="amount" class="form-control" value="{{ old('amount') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="revenue_type" class="form-label">Type</label>
                            <select id="revenue_type" name="revenue_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="membership">Membership</option>
                                <option value="personal_training">Personal Training</option>
                                <option value="class_booking">Class Booking</option>
                                <option value="product_sale">Product Sale</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select id="payment_method" name="payment_method" class="form-select">
                                <option value="">Select</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_payment">Mobile Payment</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="receipt" class="form-label">Receipt (optional)</label>
                            <input type="file" id="receipt" name="receipt" class="form-control" accept="image/*,.pdf">
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('gym.gym-revenues.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Revenue</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

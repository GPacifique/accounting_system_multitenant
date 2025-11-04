@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-edit me-2 text-warning"></i>
                    Edit Transaction
                </h2>
                <p class="text-muted mb-0">Update transaction details</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="enhanced-button-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Transactions
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-enhanced">
                <form action="{{ route('transactions.update', $transaction) }}" method="POST" id="transactionForm">
                    @csrf
                    @method('PUT')

                    {{-- Reference --}}
                    <div class="mb-4">
                        <label for="reference" class="form-label fw-semibold">
                            <i class="fas fa-hashtag me-1 text-muted"></i>
                            Reference Number
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-barcode"></i>
                            </span>
                            <input type="text" 
                                   class="form-control @error('reference') is-invalid @enderror" 
                                   id="reference" 
                                   name="reference"
                                   value="{{ old('reference', $transaction->reference) }}" 
                                   required>
                        </div>
                        @error('reference') 
                            <div class="invalid-feedback d-block">{{ $message }}</div> 
                        @enderror
                    </div>

                    <div class="row g-3">
                        {{-- Transaction Date --}}
                        <div class="col-md-6">
                            <label for="date" class="form-label fw-semibold">
                                <i class="fas fa-calendar me-1 text-muted"></i>
                                Transaction Date
                            </label>
                            <input type="date" 
                                   class="form-control @error('date') is-invalid @enderror" 
                                   id="date" 
                                   name="date"
                                   value="{{ old('date', $transaction->date->format('Y-m-d')) }}" 
                                   required>
                            @error('date') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        {{-- Transaction Type --}}
                        <div class="col-md-6">
                            <label for="type" class="form-label fw-semibold">
                                <i class="fas fa-tag me-1 text-muted"></i>
                                Transaction Type
                            </label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" 
                                    name="type" 
                                    required>
                                <option value="">Select type...</option>
                                <option value="revenue" {{ old('type', $transaction->type) == 'revenue' ? 'selected' : '' }}>
                                    💰 Revenue / Income
                                </option>
                                <option value="expense" {{ old('type', $transaction->type) == 'expense' ? 'selected' : '' }}>
                                    💸 Expense
                                </option>
                                <option value="payroll" {{ old('type', $transaction->type) == 'payroll' ? 'selected' : '' }}>
                                    👥 Payroll
                                </option>
                                <option value="transfer" {{ old('type', $transaction->type) == 'transfer' ? 'selected' : '' }}>
                                    🔄 Transfer
                                </option>
                            </select>
                            @error('type') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        {{-- Category --}}
                        <div class="col-md-6">
                            <label for="category" class="form-label fw-semibold">
                                <i class="fas fa-folder me-1 text-muted"></i>
                                Category
                            </label>
                            <select class="form-select @error('category') is-invalid @enderror" 
                                    id="category" 
                                    name="category">
                                <option value="">Select category...</option>
                                <optgroup label="Revenue Categories">
                                    <option value="sales" {{ old('category', $transaction->category) == 'sales' ? 'selected' : '' }}>Sales</option>
                                    <option value="services" {{ old('category', $transaction->category) == 'services' ? 'selected' : '' }}>Services</option>
                                    <option value="commissions" {{ old('category', $transaction->category) == 'commissions' ? 'selected' : '' }}>Commissions</option>
                                    <option value="other_income" {{ old('category', $transaction->category) == 'other_income' ? 'selected' : '' }}>Other Income</option>
                                </optgroup>
                                <optgroup label="Expense Categories">
                                    <option value="rent" {{ old('category', $transaction->category) == 'rent' ? 'selected' : '' }}>Rent</option>
                                    <option value="utilities" {{ old('category', $transaction->category) == 'utilities' ? 'selected' : '' }}>Utilities</option>
                                    <option value="supplies" {{ old('category', $transaction->category) == 'supplies' ? 'selected' : '' }}>Supplies</option>
                                    <option value="equipment" {{ old('category', $transaction->category) == 'equipment' ? 'selected' : '' }}>Equipment</option>
                                    <option value="marketing" {{ old('category', $transaction->category) == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="insurance" {{ old('category', $transaction->category) == 'insurance' ? 'selected' : '' }}>Insurance</option>
                                    <option value="taxes" {{ old('category', $transaction->category) == 'taxes' ? 'selected' : '' }}>Taxes</option>
                                </optgroup>
                            </select>
                            @error('category') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>

                        {{-- Amount --}}
                        <div class="col-md-6">
                            <label for="amount" class="form-label fw-semibold">
                                <i class="fas fa-money-bill-wave me-1 text-muted"></i>
                                Amount (RWF)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">RWF</span>
                                <input type="number" 
                                       step="0.01" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" 
                                       name="amount"
                                       value="{{ old('amount', $transaction->amount) }}" 
                                       placeholder="0.00"
                                       required>
                            </div>
                            @error('amount') 
                                <div class="invalid-feedback d-block">{{ $message }}</div> 
                            @enderror
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="mb-4 mt-3">
                        <label for="notes" class="form-label fw-semibold">
                            <i class="fas fa-sticky-note me-1 text-muted"></i>
                            Notes / Description
                        </label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" 
                                  name="notes" 
                                  rows="4"
                                  placeholder="Add any additional details about this transaction...">{{ old('notes', $transaction->notes) }}</textarea>
                        @error('notes') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('transactions.index') }}" class="btn btn-lg btn-light">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="enhanced-button-primary btn-lg">
                            <i class="fas fa-save me-2"></i> Update Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format amount input
        const amountInput = document.getElementById('amount');
        amountInput.addEventListener('blur', function() {
            if (this.value) {
                const value = parseFloat(this.value);
                if (!isNaN(value)) {
                    this.value = value.toFixed(2);
                }
            }
        });
    });

    // Form validation before submit
    document.getElementById('transactionForm').addEventListener('submit', function(e) {
        const amount = parseFloat(document.getElementById('amount').value);
        if (amount <= 0) {
            e.preventDefault();
            alert('Amount must be greater than zero');
            return false;
        }
    });
</script>
@endpush
@endsection

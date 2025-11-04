@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-plus-circle me-2 text-success"></i>
                    Create New Transaction
                </h2>
                <p class="text-muted mb-0">Record a new financial transaction</p>
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
                <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
                    @csrf

                    {{-- Reference (Auto-generated) --}}
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
                                   value="{{ old('reference', $autoReference ?? '') }}" 
                                   readonly
                                   style="background-color: #f8f9fa;">
                            <button type="button" class="btn btn-outline-secondary" onclick="generateReference()">
                                <i class="fas fa-sync-alt me-1"></i> Generate
                            </button>
                        </div>
                        <small class="text-muted">Auto-generated unique reference number</small>
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
                                   value="{{ old('date', date('Y-m-d')) }}" 
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
                                <option value="revenue" {{ old('type') == 'revenue' ? 'selected' : '' }}>
                                    💰 Revenue / Income
                                </option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>
                                    💸 Expense
                                </option>
                                <option value="payroll" {{ old('type') == 'payroll' ? 'selected' : '' }}>
                                    👥 Payroll
                                </option>
                                <option value="transfer" {{ old('type') == 'transfer' ? 'selected' : '' }}>
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
                                    <option value="sales" {{ old('category') == 'sales' ? 'selected' : '' }}>Sales</option>
                                    <option value="services" {{ old('category') == 'services' ? 'selected' : '' }}>Services</option>
                                    <option value="commissions" {{ old('category') == 'commissions' ? 'selected' : '' }}>Commissions</option>
                                    <option value="other_income" {{ old('category') == 'other_income' ? 'selected' : '' }}>Other Income</option>
                                </optgroup>
                                <optgroup label="Expense Categories">
                                    <option value="rent" {{ old('category') == 'rent' ? 'selected' : '' }}>Rent</option>
                                    <option value="utilities" {{ old('category') == 'utilities' ? 'selected' : '' }}>Utilities</option>
                                    <option value="supplies" {{ old('category') == 'supplies' ? 'selected' : '' }}>Supplies</option>
                                    <option value="equipment" {{ old('category') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                                    <option value="marketing" {{ old('category') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="insurance" {{ old('category') == 'insurance' ? 'selected' : '' }}>Insurance</option>
                                    <option value="taxes" {{ old('category') == 'taxes' ? 'selected' : '' }}>Taxes</option>
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
                                       value="{{ old('amount') }}" 
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
                                  placeholder="Add any additional details about this transaction...">{{ old('notes') }}</textarea>
                        @error('notes') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('transactions.index') }}" class="btn btn-lg btn-light">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="enhanced-button-primary btn-lg" data-loading-text="Saving...">
                            <i class="fas fa-save me-2"></i> Save Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Generate automatic reference number
    function generateReference() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        
        const reference = `TXN-${year}${month}${day}-${hours}${minutes}${seconds}-${random}`;
        document.getElementById('reference').value = reference;
    }

    // Auto-generate reference on page load if empty
    document.addEventListener('DOMContentLoaded', function() {
        const referenceInput = document.getElementById('reference');
        if (!referenceInput.value) {
            generateReference();
        }

        // Update categories based on type selection
        const typeSelect = document.getElementById('type');
        const categorySelect = document.getElementById('category');
        
        typeSelect.addEventListener('change', function() {
            const type = this.value;
            categorySelect.value = ''; // Reset category
            
            // You can add logic to show/hide relevant categories
            if (type === 'revenue') {
                categorySelect.focus();
            } else if (type === 'expense') {
                categorySelect.focus();
            }
        });

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

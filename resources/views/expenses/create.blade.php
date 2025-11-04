@extends('layouts.app')
@section('title','New Expense')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-receipt me-2 text-danger"></i>
                    Create New Expense
                </h2>
                <p class="text-muted mb-0">Record a new expense transaction</p>
            </div>
            <a href="{{ route('expenses.index') }}" class="enhanced-button-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Expenses
            </a>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="form-enhanced">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <form action="{{ route('expenses.store') }}" method="POST" id="expenseForm">
                    @csrf

                    <div class="row g-3">
                        <!-- Reference -->
                        <div class="col-12">
                            <label for="reference" class="form-label fw-semibold">
                                <i class="fas fa-hashtag me-1 text-muted"></i>
                                Reference Number
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-barcode"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       id="reference" 
                                       name="reference"
                                       value="{{ old('reference', 'EXP-' . date('Ymd-His')) }}" 
                                       readonly
                                       style="background-color: #f8f9fa;">
                            </div>
                            <small class="text-muted">Auto-generated reference</small>
                        </div>

                        <!-- Date -->
                        <div class="col-md-6">
                            <label for="date" class="form-label fw-semibold">
                                <i class="fas fa-calendar me-1 text-muted"></i>
                                Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   id="date" 
                                   name="date"
                                   value="{{ old('date', date('Y-m-d')) }}"
                                   class="form-control @error('date') is-invalid @enderror"
                                   required>
                            @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Category -->
                        <div class="col-md-6">
                            <label for="category" class="form-label fw-semibold">
                                <i class="fas fa-folder me-1 text-muted"></i>
                                Category
                            </label>
                            <select id="category" 
                                    name="category" 
                                    class="form-select @error('category') is-invalid @enderror">
                                <option value="">Select category...</option>
                                @foreach($categories ?? ['Rent', 'Utilities', 'Supplies', 'Equipment', 'Salaries', 'Transport', 'Marketing', 'Other'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6">
                            <label for="amount" class="form-label fw-semibold">
                                <i class="fas fa-money-bill-wave me-1 text-muted"></i>
                                Amount (RWF) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">RWF</span>
                                <input type="number" 
                                       step="0.01" 
                                       id="amount" 
                                       name="amount"
                                       value="{{ old('amount') }}"
                                       placeholder="0.00"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       required>
                            </div>
                            @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-6">
                            <label for="method" class="form-label fw-semibold">
                                <i class="fas fa-credit-card me-1 text-muted"></i>
                                Payment Method
                            </label>
                            <select id="method" 
                                    name="method" 
                                    class="form-select @error('method') is-invalid @enderror">
                                <option value="">Select method...</option>
                                <option value="Cash" {{ old('method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Bank Transfer" {{ old('method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="Mobile Money" {{ old('method') == 'Mobile Money' ? 'selected' : '' }}>Mobile Money</option>
                                <option value="Check" {{ old('method') == 'Check' ? 'selected' : '' }}>Check</option>
                            </select>
                            @error('method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Project -->
                        <div class="col-md-6">
                            <label for="project_id" class="form-label fw-semibold">
                                <i class="fas fa-project-diagram me-1 text-muted"></i>
                                Project
                            </label>
                            <select id="project_id" 
                                    name="project_id" 
                                    class="form-select @error('project_id') is-invalid @enderror">
                                <option value="">— None —</option>
                                @foreach($projects ?? [] as $id => $name)
                                    <option value="{{ $id }}" {{ old('project_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Client/Vendor -->
                        <div class="col-md-6">
                            <label for="client_id" class="form-label fw-semibold">
                                <i class="fas fa-user-tie me-1 text-muted"></i>
                                Vendor / Supplier
                            </label>
                            <select id="client_id" 
                                    name="client_id" 
                                    class="form-select @error('client_id') is-invalid @enderror">
                                <option value="">— None —</option>
                                @foreach($clients ?? [] as $id => $name)
                                    <option value="{{ $id }}" {{ old('client_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('client_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">
                                <i class="fas fa-sticky-note me-1 text-muted"></i>
                                Description / Notes
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Add any additional details...">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                        <a href="{{ route('expenses.index') }}" class="btn btn-lg btn-light">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="enhanced-button-primary btn-lg" data-loading-text="Saving...">
                            <i class="fas fa-save me-2"></i> Save Expense
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
</script>
@endpush
@endsection

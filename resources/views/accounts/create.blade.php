@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-plus-circle me-2"></i>
            Create New Account
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('accounts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Accounts
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Account Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('accounts.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="code" class="form-label">Account Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code') }}" required maxlength="20"
                                       placeholder="e.g., 1000, ACC001">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Unique identifier for this account</small>
                            </div>
                            <div class="col-md-6">
                                <label for="type" class="form-label">Account Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">Select Account Type</option>
                                    @foreach($accountTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Account Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required maxlength="255"
                                   placeholder="e.g., Cash in Bank, Accounts Receivable">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Parent Account</label>
                            <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                <option value="">No Parent (Top Level Account)</option>
                                @foreach($parentAccounts as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->code }} - {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">For creating sub-accounts under existing accounts</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Optional description for this account">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="opening_balance" class="form-label">Opening Balance</label>
                                <div class="input-group">
                                    <input type="number" name="opening_balance" id="opening_balance" 
                                           class="form-control @error('opening_balance') is-invalid @enderror" 
                                           value="{{ old('opening_balance', '0.00') }}" step="0.01"
                                           placeholder="0.00">
                                    <span class="input-group-text">
                                        <span id="currency-display">USD</span>
                                    </span>
                                </div>
                                @error('opening_balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="currency" class="form-label">Currency</label>
                                <select name="currency" id="currency" class="form-select @error('currency') is-invalid @enderror">
                                    <option value="USD" {{ old('currency', 'USD') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                    <option value="RWF" {{ old('currency') == 'RWF' ? 'selected' : '' }}>RWF - Rwandan Franc</option>
                                    <option value="KES" {{ old('currency') == 'KES' ? 'selected' : '' }}>KES - Kenyan Shilling</option>
                                    <option value="UGX" {{ old('currency') == 'UGX' ? 'selected' : '' }}>UGX - Ugandan Shilling</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                            <input type="number" name="tax_rate" id="tax_rate" 
                                   class="form-control @error('tax_rate') is-invalid @enderror" 
                                   value="{{ old('tax_rate') }}" step="0.01" min="0" max="100"
                                   placeholder="0.00">
                            @error('tax_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Default tax rate for transactions in this account</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Account
                                </label>
                            </div>
                            <small class="form-text text-muted">Inactive accounts won't be available for new transactions</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Create Account
                            </button>
                            <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Type Information -->
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Account Type Guide</h6>
                </div>
                <div class="card-body">
                    <div id="account-type-info">
                        <p class="text-muted">Select an account type to see its description and examples.</p>
                    </div>

                    <div class="mt-4">
                        <h6 class="font-weight-bold">Account Coding Tips:</h6>
                        <ul class="small text-muted">
                            <li><strong>Assets:</strong> 1000-1999 (e.g., 1000 = Cash)</li>
                            <li><strong>Liabilities:</strong> 2000-2999 (e.g., 2000 = Accounts Payable)</li>
                            <li><strong>Equity:</strong> 3000-3999 (e.g., 3000 = Owner's Equity)</li>
                            <li><strong>Revenue:</strong> 4000-4999 (e.g., 4000 = Sales Revenue)</li>
                            <li><strong>Expenses:</strong> 5000-5999 (e.g., 5000 = Office Supplies)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Account Hierarchy</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        You can create sub-accounts by selecting a parent account. For example:
                    </p>
                    <div class="small">
                        <strong>1000 - Cash & Bank</strong><br>
                        &nbsp;&nbsp;├─ 1001 - Petty Cash<br>
                        &nbsp;&nbsp;├─ 1002 - Bank of Kigali<br>
                        &nbsp;&nbsp;└─ 1003 - Equity Bank
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const accountTypeInfo = {
        'asset': {
            description: 'Assets are resources owned by the business that have economic value.',
            examples: ['Cash in Bank', 'Accounts Receivable', 'Inventory', 'Equipment', 'Buildings']
        },
        'liability': {
            description: 'Liabilities are debts or obligations owed by the business to others.',
            examples: ['Accounts Payable', 'Bank Loans', 'Credit Cards', 'Accrued Expenses', 'Mortgages']
        },
        'equity': {
            description: 'Equity represents the owner\'s stake in the business.',
            examples: ['Owner\'s Capital', 'Retained Earnings', 'Common Stock', 'Additional Paid-in Capital']
        },
        'revenue': {
            description: 'Revenue accounts track income earned by the business.',
            examples: ['Sales Revenue', 'Service Income', 'Interest Income', 'Rental Income', 'Commission Income']
        },
        'expense': {
            description: 'Expense accounts track costs incurred in operating the business.',
            examples: ['Office Supplies', 'Rent Expense', 'Utilities', 'Salaries', 'Marketing Expenses']
        }
    };

    $('#type').on('change', function() {
        const selectedType = $(this).val();
        const infoDiv = $('#account-type-info');
        
        if (selectedType && accountTypeInfo[selectedType]) {
            const info = accountTypeInfo[selectedType];
            let html = `
                <h6 class="font-weight-bold text-primary">${selectedType.charAt(0).toUpperCase() + selectedType.slice(1)} Accounts</h6>
                <p class="small">${info.description}</p>
                <h6 class="font-weight-bold">Examples:</h6>
                <ul class="small">
            `;
            
            info.examples.forEach(example => {
                html += `<li>${example}</li>`;
            });
            
            html += '</ul>';
            infoDiv.html(html);
        } else {
            infoDiv.html('<p class="text-muted">Select an account type to see its description and examples.</p>');
        }
    });

    // Update currency display
    $('#currency').on('change', function() {
        $('#currency-display').text($(this).val());
    });

    // Initialize currency display
    $('#currency-display').text($('#currency').val());
});
</script>
@endpush
@endsection
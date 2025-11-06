@php
    // $payment may be null in create view
    $isEdit = isset($payment);
@endphp

<form action="{{ $isEdit ? route('payments.update', $payment) : route('payments.store') }}" method="POST">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="mb-3">
        <label for="reference" class="form-label">
            <i class="fa-solid fa-hashtag me-1"></i>Payment Reference 
            <small class="text-muted">(auto-generated)</small>
        </label>
        <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-barcode"></i></span>
            <input id="reference" name="reference" type="text"
                   class="form-control @error('reference') is-invalid @enderror"
                   value="{{ old('reference', $payment->reference ?? '') }}" 
                   readonly="{{ !$isEdit ? 'true' : 'false' }}"
                   placeholder="Auto-generated reference number">
            @if(!$isEdit)
            <button type="button" id="generateRef" class="btn btn-outline-primary" title="Generate New Reference">
                <i class="fa-solid fa-rotate-right"></i>
            </button>
            @endif
            @error('reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <small class="text-muted">
            <i class="fa-solid fa-info-circle me-1"></i>
            Format: PAY-YYYYMMDD-HHMMSS-XXXX (automatically generated for new payments)
        </small>
    </div>

    <div class="mb-3">
        <label for="employee_id" class="form-label">Employee</label>
        <select id="employee_id" name="employee_id" class="form-select @error('employee_id') is-invalid @enderror">
            <option value="">— None —</option>
            @foreach(\App\Models\Employee::orderBy('first_name')->get() as $employee)
                <option value="{{ $employee->id }}"
                    {{ old('employee_id', $payment->employee_id ?? '') == $employee->id ? 'selected' : '' }}>
                    {{ $employee->full_name }}
                </option>
            @endforeach
        </select>
        @error('employee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label for="method" class="form-label">
                <i class="fa-solid fa-credit-card me-1"></i>Payment Method
            </label>
            <select id="method" name="method" class="form-select @error('method') is-invalid @enderror" required>
                <option value="">— Select Payment Method —</option>
                <option value="cash" {{ old('method', $payment->method ?? '') === 'cash' ? 'selected' : '' }}>
                    💵 Cash
                </option>
                <option value="bank_transfer" {{ old('method', $payment->method ?? '') === 'bank_transfer' ? 'selected' : '' }}>
                    🏦 Bank Transfer
                </option>
                <option value="mobile_money" {{ old('method', $payment->method ?? '') === 'mobile_money' ? 'selected' : '' }}>
                    📱 Mobile Money
                </option>
                <option value="credit_card" {{ old('method', $payment->method ?? '') === 'credit_card' ? 'selected' : '' }}>
                    💳 Credit Card
                </option>
                <option value="debit_card" {{ old('method', $payment->method ?? '') === 'debit_card' ? 'selected' : '' }}>
                    💳 Debit Card
                </option>
                <option value="check" {{ old('method', $payment->method ?? '') === 'check' ? 'selected' : '' }}>
                    📝 Check/Cheque
                </option>
                <option value="wire_transfer" {{ old('method', $payment->method ?? '') === 'wire_transfer' ? 'selected' : '' }}>
                    🌐 Wire Transfer
                </option>
                <option value="paypal" {{ old('method', $payment->method ?? '') === 'paypal' ? 'selected' : '' }}>
                    🅿️ PayPal
                </option>
                <option value="crypto" {{ old('method', $payment->method ?? '') === 'crypto' ? 'selected' : '' }}>
                    ₿ Cryptocurrency
                </option>
                <option value="other" {{ old('method', $payment->method ?? '') === 'other' ? 'selected' : '' }}>
                    ❓ Other
                </option>
            </select>
            @error('method') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <small class="text-muted">Select the payment method used for this transaction</small>
            
            <!-- Custom method input (shown when "Other" is selected) -->
            <div id="customMethodDiv" class="mt-2" style="display: none;">
                <label for="custom_method" class="form-label">
                    <i class="fa-solid fa-edit me-1"></i>Custom Payment Method
                </label>
                <input id="custom_method" name="custom_method" type="text" 
                       class="form-control" 
                       placeholder="Enter custom payment method"
                       value="{{ old('custom_method') }}">
                <small class="text-muted">Specify the custom payment method</small>
            </div>
        </div>

        <div class="col-md-6">
            <label for="amount" class="form-label">Amount</label>
            <input id="amount" name="amount" type="number" step="0.01"
                   class="form-control @error('amount') is-invalid @enderror"
                   value="{{ old('amount', isset($payment) ? $payment->amount : '') }}" required>
            @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mb-3 mt-3">
        <label for="status" class="form-label">Status</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
            <option value="pending" {{ old('status', $payment->status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="completed" {{ old('status', $payment->status ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="failed" {{ old('status', $payment->status ?? '') === 'failed' ? 'selected' : '' }}>Failed</option>
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-success">{{ $isEdit ? 'Update' : 'Save' }}</button>
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

@if(!$isEdit)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate reference on page load for new payments
    generatePaymentReference();
    
    // Handle manual generation button click
    document.getElementById('generateRef').addEventListener('click', function() {
        generatePaymentReference();
        
        // Visual feedback
        const button = this;
        const originalIcon = button.innerHTML;
        button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        button.disabled = true;
        
        setTimeout(() => {
            button.innerHTML = '<i class="fa-solid fa-check text-success"></i>';
            button.classList.add('btn-success');
            button.classList.remove('btn-outline-primary');
            
            setTimeout(() => {
                button.innerHTML = originalIcon;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-primary');
                button.disabled = false;
            }, 1000);
        }, 500);
    });
    
    function generatePaymentReference() {
        // Try backend endpoint first, fallback to frontend generation
        fetch('{{ route("payments.generate-reference") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('reference').value = data.reference;
                highlightReferenceField();
            })
            .catch(error => {
                console.log('Backend generation failed, using frontend fallback');
                // Fallback to frontend generation
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                
                // Generate random 4-digit suffix
                const randomSuffix = Math.floor(Math.random() * 9000) + 1000;
                
                // Format: PAY-YYYYMMDD-HHMMSS-XXXX
                const reference = `PAY-${year}${month}${day}-${hours}${minutes}${seconds}-${randomSuffix}`;
                
                document.getElementById('reference').value = reference;
                highlightReferenceField();
            });
    }
    
    function highlightReferenceField() {
        const referenceInput = document.getElementById('reference');
        
        // Add subtle animation to show the field was updated
        referenceInput.style.transition = 'background-color 0.3s ease';
        referenceInput.style.backgroundColor = '#d4edda';
        setTimeout(() => {
            referenceInput.style.backgroundColor = '';
        }, 1500);
    }
    
    // Add copy functionality
    document.getElementById('reference').addEventListener('click', function() {
        if (this.value) {
            navigator.clipboard.writeText(this.value).then(() => {
                // Show copied feedback
                const originalBg = this.style.backgroundColor;
                this.style.backgroundColor = '#cce7ff';
                this.title = 'Reference copied to clipboard!';
                setTimeout(() => {
                    this.style.backgroundColor = originalBg;
                    this.title = 'Click to copy reference';
                }, 1000);
            }).catch(() => {
                // Fallback for older browsers
                this.select();
                document.execCommand('copy');
                this.style.backgroundColor = '#cce7ff';
                setTimeout(() => {
                    this.style.backgroundColor = '';
                }, 1000);
            });
        }
    });
    
    // Set initial title
    document.getElementById('reference').title = 'Click to copy reference';
});
</script>
@endif

<!-- Payment Method Dropdown Handler (works for both create and edit) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const methodSelect = document.getElementById('method');
    const customMethodDiv = document.getElementById('customMethodDiv');
    const customMethodInput = document.getElementById('custom_method');
    
    // Handle method selection changes
    methodSelect.addEventListener('change', function() {
        if (this.value === 'other') {
            customMethodDiv.style.display = 'block';
            customMethodInput.setAttribute('required', 'required');
            customMethodInput.focus();
        } else {
            customMethodDiv.style.display = 'none';
            customMethodInput.removeAttribute('required');
            customMethodInput.value = '';
        }
    });
    
    // Check initial state (for edit forms)
    if (methodSelect.value === 'other') {
        customMethodDiv.style.display = 'block';
        customMethodInput.setAttribute('required', 'required');
    }
    
    // Handle form submission to use custom method if provided
    const form = methodSelect.closest('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (methodSelect.value === 'other' && customMethodInput.value.trim()) {
                // Create a hidden input to send the custom method as the actual method
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'method';
                hiddenInput.value = customMethodInput.value.trim();
                form.appendChild(hiddenInput);
                
                // Remove the required attribute from the select to avoid conflicts
                methodSelect.removeAttribute('name');
            }
        });
    }
    
    // Add visual enhancement for different payment methods
    methodSelect.addEventListener('change', function() {
        const methodIcon = this.previousElementSibling.querySelector('i');
        
        // Update icon based on selected method
        switch(this.value) {
            case 'cash':
                methodIcon.className = 'fa-solid fa-money-bill me-1';
                break;
            case 'bank_transfer':
                methodIcon.className = 'fa-solid fa-university me-1';
                break;
            case 'mobile_money':
                methodIcon.className = 'fa-solid fa-mobile-alt me-1';
                break;
            case 'credit_card':
            case 'debit_card':
                methodIcon.className = 'fa-solid fa-credit-card me-1';
                break;
            case 'check':
                methodIcon.className = 'fa-solid fa-file-invoice me-1';
                break;
            case 'wire_transfer':
                methodIcon.className = 'fa-solid fa-globe me-1';
                break;
            case 'paypal':
                methodIcon.className = 'fa-brands fa-paypal me-1';
                break;
            case 'crypto':
                methodIcon.className = 'fa-brands fa-bitcoin me-1';
                break;
            case 'other':
                methodIcon.className = 'fa-solid fa-question me-1';
                break;
            default:
                methodIcon.className = 'fa-solid fa-credit-card me-1';
        }
    });
});
</script>

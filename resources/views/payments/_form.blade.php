@php
    // $payment may be null in create view
    $isEdit = isset($payment);
@endphp

<form action="{{ $isEdit ? route('payments.update', $payment) : route('payments.store') }}" method="POST">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="mb-3">
        <label for="reference" class="form-label">Reference</label>
        <input id="reference" name="reference" type="text"
               class="form-control @error('reference') is-invalid @enderror"
               value="{{ old('reference', $payment->reference ?? '') }}">
        @error('reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
            <label for="method" class="form-label">Method</label>
            <input id="method" name="method" type="text"
                   class="form-control @error('method') is-invalid @enderror"
                   value="{{ old('method', $payment->method ?? '') }}">
            @error('method') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

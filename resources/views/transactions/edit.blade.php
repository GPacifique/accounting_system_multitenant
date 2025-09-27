@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Transaction #{{ $transaction->id }}</h1>

    <form action="{{ route('transactions.update', $transaction) }}" method="POST">
        @csrf
        @method('PUT') {{-- or PATCH --}}

        <div class="mb-3">
            <label for="reference" class="form-label">Reference</label>
            <input type="text" class="form-control" id="reference" name="reference"
                   value="{{ old('reference', $transaction->reference) }}" required>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" step="0.01" class="form-control" id="amount" name="amount"
                   value="{{ old('amount', $transaction->amount) }}" required>
        </div>

        <div class="mb-3">
            <label for="currency" class="form-label">Currency</label>
            <input type="text" class="form-control" id="currency" name="currency"
                   value="{{ old('currency', $transaction->currency) }}">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $transaction->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Transaction</button>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Transaction #{{ $transaction->id }}</h1>

    <div class="mb-3">
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary btn-sm">← Back to list</a>
        <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-warning btn-sm">Edit</a>

        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this transaction?');">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm">Delete</button>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <p><strong>Reference:</strong> {{ $transaction->reference ?? '—' }}</p>
            <p><strong>Amount:</strong> {{ number_format($transaction->amount, 2) }}</p>
            <p><strong>Currency:</strong> {{ $transaction->currency ?? '—' }}</p>
            <p><strong>Date:</strong> {{ optional($transaction->created_at)->toDayDateTimeString() }}</p>
            <p><strong>Description:</strong> {{ $transaction->description ?? '—' }}</p>

            {{-- Example: if your model has relation with user --}}
            @if($transaction->user)
                <p><strong>Created By:</strong> {{ $transaction->user->name }}</p>
            @endif
        </div>
    </div>
</div>
@endsection

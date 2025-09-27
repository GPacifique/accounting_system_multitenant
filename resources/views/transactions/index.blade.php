@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Transactions</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('transactions.create') }}" class="btn btn-primary">+ New Transaction</a>
    </div>

    @if(isset($transactions) && $transactions->count())
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Currency</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td>{{ $t->reference ?? '—' }}</td>
                            <td>{{ number_format($t->amount, 2) }}</td>
                            <td>{{ $t->currency ?? '—' }}</td>
                            <td>{{ optional($t->created_at)->toDateString() }}</td>
                            <td class="text-end">
                                <a href="{{ route('transactions.show', $t) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('transactions.edit', $t) }}" class="btn btn-sm btn-warning">Edit</a>

                                <form action="{{ route('transactions.destroy', $t) }}" method="POST" class="d-inline-block" 
                                      onsubmit="return confirm('Are you sure you want to delete this transaction?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $transactions->links() }}
        </div>
    @else
        <div class="alert alert-info">
            No transactions found.
        </div>

        {{-- Example stub row if you want a placeholder table instead of the alert:
        <table class="table">
            <thead><tr><th>ID</th><th>Amount</th><th>Date</th></tr></thead>
            <tbody><tr><td>1</td><td>100.00</td><td>{{ now()->toDateString() }}</td></tr></tbody>
        </table>
        --}}
    @endif
</div>
@endsection

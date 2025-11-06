@extends('layouts.app')
@section('title', 'Transaction Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold theme-aware-text leading-tight">Transaction #{{ $transaction->id }}</h1>
                <p class="text-sm theme-aware-text-muted mt-1">{{ optional($transaction->created_at)->toDayDateTimeString() }}</p>
            </div>
            <div class="flex items-center gap-2 mt-4 sm:mt-0">
                <a href="{{ route('transactions.edit', $transaction) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('transactions.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transaction?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn-danger">
                        <i class="fas fa-trash-alt mr-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Details Card -->
        <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium theme-aware-text-muted">Reference</h3>
                        <p class="mt-1 theme-aware-text">{{ $transaction->reference ?? '—' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium theme-aware-text-muted">Amount</h3>
                        <p class="mt-1 theme-aware-text">{{ number_format($transaction->amount, 2) }} {{ $transaction->currency ?? 'RWF' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-medium theme-aware-text-muted">Description</h3>
                        <p class="mt-1 theme-aware-text">{{ $transaction->description ?? '—' }}</p>
                    </div>
                    @if($transaction->user)
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-medium theme-aware-text-muted">Created By</h3>
                            <p class="mt-1 theme-aware-text">{{ $transaction->user->name }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary {
        @apply inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    .btn-secondary {
        @apply inline-flex items-center px-4 py-2 theme-aware-bg-tertiary border theme-aware-border rounded-md font-semibold text-xs theme-aware-text-secondary uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:theme-aware-border-secondary focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    .btn-danger {
        @apply inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
</style>
@endpush

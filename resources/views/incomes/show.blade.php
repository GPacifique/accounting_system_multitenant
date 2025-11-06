@extends('layouts.app')
@section('title', 'Income Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold theme-aware-text leading-tight">Income #{{ $income->id }}</h1>
                <p class="text-sm theme-aware-text-muted mt-1">Received {{ optional($income->received_at)->diffForHumans() }}</p>
            </div>
            <div class="mt-4 sm:mt-0 text-right">
                <div class="text-2xl font-extrabold text-green-600">RWF {{ number_format($income->amount_received,2) }}</div>
                <div class="text-xs theme-aware-text-muted">Remaining: RWF {{ number_format($income->amount_remaining,2) }}</div>
            </div>
        </div>

        <!-- Details Card -->
        <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold theme-aware-text-secondary border-b pb-2 mb-4">Income Info</h3>
                        <dl class="space-y-4">
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Invoice Number</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ $income->invoice_number ?? '—' }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Payment Status</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ ucfirst($income->payment_status ?? '—') }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Received At</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ optional($income->received_at)->format('Y-m-d') ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold theme-aware-text-secondary border-b pb-2 mb-4">Associations</h3>
                        <dl class="space-y-4">
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Project</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ $income->project->name ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Notes</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ $income->notes ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex flex-wrap gap-2">
            <a href="{{ route('incomes.edit', $income) }}" class="btn-primary">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('incomes.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
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
</style>
@endpush

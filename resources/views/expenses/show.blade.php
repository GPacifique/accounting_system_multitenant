@extends('layouts.app')
@section('title','Expense Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold theme-aware-text leading-tight">Expense #{{ $expense->id }}</h1>
                <p class="text-sm theme-aware-text-muted mt-1">Added {{ optional($expense->created_at)->diffForHumans() }}</p>
            </div>
            <div class="mt-4 sm:mt-0 text-right">
                <div class="text-2xl font-extrabold text-red-600">RWF {{ number_format($expense->amount,2) }}</div>
                <div class="text-xs theme-aware-text-muted">{{ optional($expense->date)->format('Y-m-d') }}</div>
            </div>
        </div>

        <!-- Details Card -->
        <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold theme-aware-text-secondary border-b pb-2 mb-4">Expense Info</h3>
                        <dl class="space-y-4">
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Date</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ optional($expense->date)->format('Y-m-d') ?? '—' }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Category</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ $expense->category ?? '—' }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Method</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ $expense->method ?? '—' }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Reference</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ $expense->reference ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold theme-aware-text-secondary border-b pb-2 mb-4">Associations</h3>
                        <dl class="space-y-4">
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Project</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ $expense->project_id ? ($expense->project->name ?? '—') : '—' }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Client</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ $expense->client_id ? ($expense->client->name ?? '—') : '—' }}</dd>
                            </div>
                            <div class="flex flex-col md:col-span-2">
                                <dt class="text-sm font-medium theme-aware-text-muted">Registered By</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ $expense->user->name ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-sm font-medium theme-aware-text-muted">Description</h3>
                    <p class="mt-1 theme-aware-text">{{ $expense->description ?? '—' }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex flex-wrap gap-2">
            <a href="{{ route('expenses.edit', $expense) }}" class="btn-primary">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Delete this expense?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger">
                    <i class="fas fa-trash-alt mr-2"></i>Delete
                </button>
            </form>
            <a href="{{ route('expenses.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back
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
    .btn-danger {
        @apply inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    .btn-secondary {
        @apply inline-flex items-center px-4 py-2 theme-aware-bg-tertiary border theme-aware-border rounded-md font-semibold text-xs theme-aware-text-secondary uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:theme-aware-border-secondary focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
</style>
@endpush

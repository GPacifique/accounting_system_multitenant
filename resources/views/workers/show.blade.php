@extends('layouts.app')
@section('title', 'Worker Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold theme-aware-text leading-tight">Worker #{{ $worker->id }}</h1>
                <p class="text-sm theme-aware-text-muted mt-1">Hired {{ optional($worker->hired_at)->format('F j, Y') ?? '—' }}</p>
            </div>
            <div class="flex items-center gap-2 mt-4 sm:mt-0">
                <a href="{{ route('workers.edit', $worker) }}" class="btn btn-primary">
                    <i class="fas fa-edit mr-2"></i><span class="btn-label">Edit</span>
                </a>
                <a href="{{ route('workers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i><span class="btn-label">Back to Workers</span>
                </a>
            </div>
        </div>

        <!-- Details Card -->
        <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium theme-aware-text-muted">Full Name</h3>
                        <p class="mt-1 theme-aware-text">{{ $worker->full_name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium theme-aware-text-muted">Position</h3>
                        <p class="mt-1 theme-aware-text">{{ $worker->position ?? '—' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium theme-aware-text-muted">Email</h3>
                        <p class="mt-1 theme-aware-text">{{ $worker->email ?? '—' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium theme-aware-text-muted">Phone</h3>
                        <p class="mt-1 theme-aware-text">{{ $worker->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium theme-aware-text-muted">Status</h3>
                        <p class="mt-1 theme-aware-text">{{ ucfirst($worker->status) }}</p>
                    </div>
                </div>

                @if($worker->notes)
                    <div class="mt-6">
                        <h3 class="text-sm font-medium theme-aware-text-muted">Notes</h3>
                        <div class="mt-1 theme-aware-text">{!! nl2br(e($worker->notes)) !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Daily Payments -->
        <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden mt-8">
            <div class="p-6 sm:p-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold theme-aware-text">Daily Payments</h2>
                    <form action="{{ route('workers.payments.bulk') }}" method="POST" class="flex items-end gap-2">
                        @csrf
                        <input type="hidden" name="worker_ids[]" value="{{ $worker->id }}">
                        <div>
                            <label class="block text-xs theme-aware-text-muted mb-1">Date</label>
                            <input type="date" name="paid_on" value="{{ now()->format('Y-m-d') }}" class="border rounded px-2 py-1 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs theme-aware-text-muted mb-1">Amount</label>
                            <input type="number" step="0.01" min="0" name="amounts[{{ $worker->id }}]" placeholder="0.00" class="border rounded px-2 py-1 text-sm">
                        </div>
                        <button type="submit" class="btn btn-success" data-loading-on-click>
                            <span class="btn-label">Add Payment</span>
                        </button>
                    </form>
                </div>

                @php $payments = $worker->payments ?? collect(); @endphp
                @if($payments->isEmpty())
                    <p class="text-sm theme-aware-text-muted">No payments recorded yet for this worker.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="theme-aware-text-secondary">
                                    <th class="py-2 px-3 text-left">Date</th>
                                    <th class="py-2 px-3 text-left">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($payments as $pay)
                                    <tr>
                                        <td class="py-2 px-3">{{ optional($pay->paid_on)->format('Y-m-d') }}</td>
                                        <td class="py-2 px-3 font-semibold text-green-600">RWF {{ number_format($pay->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Button styles are globally provided via resources/css/app.css -->
@endpush

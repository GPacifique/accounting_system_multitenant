@extends('layouts.app')
@section('title', 'Payment Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold theme-aware-text leading-tight">Payment #{{ $payment->id }}</h1>
                <p class="text-sm theme-aware-text-muted mt-1">Created {{ optional($payment->created_at)->diffForHumans() }}</p>
            </div>
            <div class="mt-4 sm:mt-0 text-right">
                <div class="text-2xl font-extrabold text-green-600">
                    {{ number_format(($payment->amount_cents ?? 0) / 100, 2) }} {{ $payment->currency ?? 'RWF' }}
                </div>
                <div class="text-xs theme-aware-text-muted">Status: {{ ucfirst($payment->status ?? '—') }}</div>
            </div>
        </div>

        <!-- Details Card -->
        <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold theme-aware-text-secondary border-b pb-2 mb-4">Payment Info</h3>
                        <dl class="space-y-4">
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Payer</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ $payment->payer_name ?? ($payment->user->email ?? '—') }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Method</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ $payment->payment_method ?? '—' }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Reference</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ $payment->reference ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold theme-aware-text-secondary border-b pb-2 mb-4">Additional</h3>
                        <dl class="space-y-4">
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Created at</dt>
                                <dd class="mt-1 text-md theme-aware-text">{{ optional($payment->created_at)->format('F j, Y g:i A') ?? '—' }}</dd>
                            </div>
                            @if (!empty($payment->receipt_url))
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Receipt</dt>
                                <dd class="mt-1 text-md text-indigo-600">
                                    <a href="{{ $payment->receipt_url }}" target="_blank" rel="noopener" class="hover:underline">Download receipt</a>
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                @if (!empty($payment->notes))
                    <div class="mt-6">
                        <h3 class="text-sm font-medium theme-aware-text-muted">Notes</h3>
                        <div class="mt-1 theme-aware-text">{!! nl2br(e($payment->notes)) !!}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex flex-wrap gap-2">
            <a href="{{ route('payments.edit', $payment) }}" class="btn-primary">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>

            @if (method_exists($payment, 'refundable') ? $payment->refundable() : (($payment->status ?? '') === 'completed'))
                <form action="{{ route('payments.refund', $payment) }}" method="POST" onsubmit="return confirm('Are you sure you want to refund this payment?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-md text-xs font-semibold hover:bg-yellow-600 transition">
                        <i class="fas fa-undo mr-2"></i>Refund
                    </button>
                </form>
            @endif

            <a href="{{ route('payments.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Payments
            </a>
        </div>

        @if (app()->environment('local'))
            <hr class="my-6" />
            <h3 class="text-lg font-semibold mb-2">Raw payment (debug)</h3>
            <pre class="bg-gray-900 text-gray-100 p-4 rounded">{{ json_encode($payment->toArray(), JSON_PRETTY_PRINT) }}</pre>
        @endif
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
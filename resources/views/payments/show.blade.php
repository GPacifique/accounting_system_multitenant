<!-- resources/views/payments/show.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Payment #{{ $payment->id }}</h1>

    <div class="card p-4 mb-4">
        <p><strong>Amount:</strong>
            {{ number_format($payment->amount_cents / 100, 2) }} {{ $payment->currency ?? '$' }}
        </p>

        <p><strong>Status:</strong> {{ ucfirst($payment->status) }}</p>

        <p><strong>Payer:</strong>
            {{ $payment->payer_name ?? ($payment->user->email ?? '—') }}
        </p>

        <p><strong>Payment method:</strong> {{ $payment->payment_method ?? '—' }}</p>

        <p><strong>Reference:</strong> {{ $payment->reference ?? '—' }}</p>

        <p><strong>Created at:</strong> {{ $payment->created_at->format('F j, Y g:i A') }}</p>

        @if (!empty($payment->notes))
            <div class="mt-3">
                <strong>Notes:</strong>
                <div>{!! nl2br(e($payment->notes)) !!}</div>
            </div>
        @endif

        @if (!empty($payment->receipt_url))
            <p><strong>Receipt:</strong>
                <a href="{{ $payment->receipt_url }}" target="_blank" rel="noopener">Download receipt</a>
            </p>
        @endif
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('payments.edit', $payment) }}" class="btn btn-primary">Edit</a>

        @if (method_exists($payment, 'refundable') ? $payment->refundable() : ($payment->status === 'completed'))
            <form action="{{ route('payments.refund', $payment) }}" method="POST" onsubmit="return confirm('Are you sure you want to refund this payment?');">
                @csrf
                <button type="submit" class="btn btn-warning">Refund</button>
            </form>
        @endif

        <a href="{{ route('payments.index') }}" class="btn btn-secondary">Back to payments</a>
    </div>

    @if (app()->environment('local'))
        <hr />
        <h3>Raw payment (debug)</h3>
        <pre>{{ json_encode($payment->toArray(), JSON_PRETTY_PRINT) }}</pre>
    @endif
</div>
@endsection
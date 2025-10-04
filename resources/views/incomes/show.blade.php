@extends('layouts.app')
@vite(['resources/css/app.css', 'resources/js/app.js'])
@vite(['resources/css/app.css', 'resources/js/app.js'])
@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Income Details</h1>

    <div class="bg-white p-6 rounded shadow-md">
        <p><strong>Project:</strong> {{ $income->project->name ?? 'N/A' }}</p>
        <p><strong>Invoice Number:</strong> {{ $income->invoice_number }}</p>
        <p><strong>Amount Received:</strong> ${{ number_format($income->amount_received, 2) }}</p>
        <p><strong>Payment Status:</strong> {{ $income->payment_status }}</p>
        <p><strong>Amount Remaining:</strong> ${{ number_format($income->amount_remaining, 2) }}</p>
        <p><strong>Received At:</strong> {{ $income->received_at->format('Y-m-d') }}</p>
        <p><strong>Notes:</strong> {{ $income->notes ?? 'N/A' }}</p>

        <div class="mt-4">
            <a href="{{ route('incomes.index') }}" class="text-blue-500 hover:underline">Back to List</a>
        </div>
    </div>
</div>
@endsection

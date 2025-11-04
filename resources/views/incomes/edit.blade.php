@extends('layouts.app')
@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Edit Income Record</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('incomes.update', $income->id) }}" method="POST" class="theme-aware-bg-card p-6 rounded shadow-md">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block font-semibold mb-1">Project</label>
            <select name="project_id" class="w-full border p-2 rounded" required>
                <option value="">Select Project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ $income->project_id == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Invoice Number</label>
            <div class="flex gap-2">
                <input id="invoice_number" type="text" name="invoice_number" value="{{ old('invoice_number', $income->invoice_number) }}" class="flex-1 border p-2 rounded" required>
                <button type="button" id="btn-generate-invoice" class="bg-gray-100 theme-aware-text px-3 py-2 rounded border">Generate</button>
            </div>
            <p class="text-xs theme-aware-text-muted mt-1">Click Generate to replace with a new auto-generated number.</p>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Amount Received</label>
            <input type="number" step="0.01" name="amount_received" value="{{ old('amount_received', $income->amount_received) }}" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Payment Status</label>
                <select name="payment_status" class="w-full border p-2 rounded" required>
                    <option value="Paid" {{ old('payment_status', $income->payment_status) == 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Pending" {{ old('payment_status', $income->payment_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partially paid" {{ old('payment_status', $income->payment_status) == 'partially paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="Overdue" {{ old('payment_status', $income->payment_status) == 'Overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Amount Remaining</label>
            <input type="number" step="0.01" name="amount_remaining" value="{{ old('amount_remaining', $income->amount_remaining) }}" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Received At</label>
            <input type="date" name="received_at" value="{{ old('received_at', $income->received_at->format('Y-m-d')) }}" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Notes</label>
            <textarea name="notes" class="w-full border p-2 rounded">{{ old('notes', $income->notes) }}</textarea>
        </div>

        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">Update Income</button>
        <a href="{{ route('incomes.index') }}" class="ml-2 theme-aware-text-secondary hover:underline">Cancel</a>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btn-generate-invoice');
            const input = document.getElementById('invoice_number');
            if (btn && input) {
                btn.addEventListener('click', function() {
                    const pad = (n) => n.toString().padStart(2, '0');
                    const d = new Date();
                    const date = d.getFullYear().toString() + pad(d.getMonth()+1) + pad(d.getDate());
                    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                    let suffix = '';
                    for (let i = 0; i < 4; i++) suffix += chars[Math.floor(Math.random()*chars.length)];
                    input.value = `INV-${date}-${suffix}`;
                });
            }
        });
    </script>
</div>
@endsection

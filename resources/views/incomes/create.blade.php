@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Add New Income</h1>

    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('incomes.store') }}" method="POST" class="bg-white p-6 rounded shadow-md">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold mb-1">Project</label>
            <select name="project_id" class="w-full border p-2 rounded">
                <option value="">Select Project</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Invoice Number</label>
            <input type="text" name="invoice_number" value="{{ old('invoice_number') }}" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Amount Received</label>
            <input type="number" step="0.01" name="amount_received" value="{{ old('amount_received') }}" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Payment Status</label>
            <select name="payment_status" class="w-full border p-2 rounded">
                <option value="Paid" {{ old('payment_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                <option value="Pending" {{ old('payment_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="partially paid" {{ old('payment_status') == 'partially paid' ? 'selected' : '' }}>Partially Paid</option>
                <option value="Overdue" {{ old('payment_status') == 'Overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Amount Remaining</label>
            <input type="number" step="0.01" name="amount_remaining" value="{{ old('amount_remaining') }}" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Received At</label>
            <input type="date" name="received_at" value="{{ old('received_at') }}" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Notes</label>
            <textarea name="notes" class="w-full border p-2 rounded">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save Income</button>
        <a href="{{ route('incomes.index') }}" class="ml-2 text-gray-600 hover:underline">Cancel</a>
    </form>
</div>
@endsection

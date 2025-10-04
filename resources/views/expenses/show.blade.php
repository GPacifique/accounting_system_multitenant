@extends('layouts.app')
@section('title','Expense Details')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-xl font-semibold">Expense #{{ $expense->id }}</h2>
                <div class="text-sm text-gray-500">Added {{ optional($expense->created_at)->diffForHumans() }}</div>
            </div>
            <div class="text-right">
                <div class="text-lg font-bold text-red-600">${{ number_format($expense->amount,2) }}</div>
                <div class="muted-small">{{ optional($expense->date)->format('Y-m-d') }}</div>
            </div>
        </div>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm text-gray-600">Date</dt>
                <dd class="font-medium">{{ $expense->date ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-600">Category</dt>
                <dd class="font-medium">{{ $expense->category ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-600">Description</dt>
                <dd class="font-medium">{{ $expense->description ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-600">Amount</dt>
                <dd class="font-medium">{{ $expense->amount ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-600">project</dt>
                <dd class="font-medium">{{ $expense->project_id ? $expense->project->name : '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-600">client</dt>
                <dd class="font-medium">{{ $expense->client_id ? $expense->client->name : '—' }}</dd>
            </div>
        
            
            <div>
                <dt class="text-sm text-gray-600">Method</dt>
                <dd class="font-medium">{{ $expense->method ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-600">status</dt>
                <dd class="font-medium">{{ $expense->reference ?? '—' }}</dd>
            </div>

            <div class="md:col-span-2">
                <dt class="text-sm text-gray-600">registered by</dt>
                <dd class="mt-2 whitespace-pre-line text-gray-700">{{ $expense->user->name ?? '-' }}</dd>
            </div>
        </dl>   

        <div class="mt-6 flex gap-2">
            <a href="{{ route('expenses.edit', $expense) }}" class="px-4 py-2 bg-green-600 text-white rounded">Edit</a>
            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Delete this expense?');">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
            </form>
            <a href="{{ route('expenses.index') }}" class="px-4 py-2 border rounded">Back</a>
        </div>
    </div>
</div>
@endsection

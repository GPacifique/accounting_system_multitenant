@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Expenses</h1>
        <a href="{{ route('expenses.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">+ New Expense</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-50 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-50 text-green-800">{{ session('success') }}</div>
    @endif

    {{-- Daily category stats --}}
    <div class="mb-6 bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold mb-3">Daily totals by category</h2>

        @if(empty($dailyTotals))
            <p class="text-sm text-gray-500">No stats available.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-2 px-3 border-r text-sm text-gray-600">Date</th>
                            @foreach($categories as $cat)
                                <th class="py-2 px-3 border-r text-sm text-gray-600">{{ $cat }}</th>
                            @endforeach
                            <th class="py-2 px-3 text-sm text-gray-600">Daily Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyTotals as $day => $cats)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="py-2 px-3 align-top font-medium">{{ $day }}</td>

                                @php $rowTotal = 0; @endphp

                                @foreach($categories as $cat)
                                    @php
                                        $amount = isset($cats[$cat]) ? $cats[$cat] : 0;
                                        $rowTotal += $amount;
                                    @endphp
                                    <td class="py-2 px-3 text-sm">
                                        @if($amount > 0)
                                            <span class="inline-block px-2 py-1 rounded text-sm font-medium bg-gray-100">
                                                RWF {{ number_format($amount, 2) }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                @endforeach

                                <td class="py-2 px-3 text-sm font-semibold text-red-600">
                                    RWF {{ number_format($rowTotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="py-3 px-4 text-sm text-gray-600">#</th>
                     <th class="py-3 px-4 text-sm text-gray-600">Date</th>
                    <th class="py-3 px-4 text-sm text-gray-600">Category</th>
                    <th class="py-3 px-4 text-sm text-gray-600">Description</th>
                    <th class="py-3 px-4 text-sm text-gray-600">Amount</th>
                    <th class="py-3 px-4 text-sm text-gray-600">Project</th>
                    <th class="py-3 px-4 text-sm text-gray-600">Client</th>
                    <th class="py-3 px-4 text-sm text-gray-600">Method</th>
                    <th class="py-3 px-4 text-sm text-gray-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $expense->id }}</td>
                        <td class="py-3 px-4">{{ optional($expense->date)->format('Y-m-d') }}</td>
                        <td class="py-3 px-4">{{ $expense->category ?? '—' }}</td>
                        <td class="py-3 px-4">{{ $expense->description ?? '—' }}</td>   
                        <td class="py-3 px-4 font-medium text-red-600">RWF {{ number_format($expense->amount, 2) }}</td>
                        
                        <td class="py-3 px-4">{{ $expense->project_id ? $expense->project->name : '—' }}</td>
                        <td class="py-3 px-4">{{ $expense->client_id ? $expense->client->name : '—' }}</td>
                        <td class="py-3 px-4 text-right">
                            <a href="{{ route('expenses.show', $expense) }}" class="text-blue-600 hover:underline">View</a>
                            <a href="{{ route('expenses.edit', $expense) }}" class="ml-2 text-green-600 hover:underline">Edit</a>
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-2 text-red-600 hover:underline" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-4 px-4 text-center text-gray-500">No expenses found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $expenses->links() }}
    </div>
</div>
@endsection

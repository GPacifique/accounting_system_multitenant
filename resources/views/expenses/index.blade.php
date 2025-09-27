@extends('layouts.app')
@vite('resources/css/app.css')
@vite('resources/js/app.js')

@section('content')
<h1 class="text-2xl font-bold mb-4">Expenses</h1>

<a href="{{ route('expenses.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Add Expense</a>

@if(session('success'))
<div class="bg-green-100 text-green-800 p-2 mb-4 rounded">{{ session('success') }}</div>
@endif

<table class="w-full border">
    <thead>
        <tr>
            <th class="border px-2 py-1">Title</th>
            <th class="border px-2 py-1">Amount</th>
            <th class="border px-2 py-1">Date</th>
            <th class="border px-2 py-1">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses as $expense)
        <tr>
            <td class="border px-2 py-1">{{ $expense->title }}</td>
            <td class="border px-2 py-1">{{ $expense->amount }}</td>
            <td class="border px-2 py-1">{{ $expense->date }}</td>
            <td class="border px-2 py-1 space-x-2">
                <a href="{{ route('expenses.show', $expense->id) }}" class="text-blue-500">View</a>
                <a href="{{ route('expenses.edit', $expense->id) }}" class="text-yellow-500">Edit</a>
                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-500" onclick="return confirm('Delete this expense?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $expenses->links() }}
@endsection

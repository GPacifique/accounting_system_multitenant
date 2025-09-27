@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Add Expense</h1>

<form action="{{ route('expenses.store') }}" method="POST" class="space-y-4">
    @csrf
    <div>
        <label>Title</label>
        <input type="text" name="title" class="border p-2 w-full" required>
    </div>
    <div>
        <label>Amount</label>
        <input type="number" step="0.01" name="amount" class="border p-2 w-full" required>
    </div>
    <div>
        <label>Date</label>
        <input type="date" name="date" class="border p-2 w-full" required>
    </div>
    <div>
        <label>Description</label>
        <textarea name="description" class="border p-2 w-full"></textarea>
    </div>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
</form>
@endsection

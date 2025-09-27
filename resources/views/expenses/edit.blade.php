@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Edit Expense</h1>

<form action="{{ route('expenses.update', $expense->id) }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label>Title</label>
        <input type="text" name="title" value="{{ $expense->title }}" class="border p-2 w-full" required>
    </div>
    <div>
        <label>Amount</label>
        <input type="number" step="0.01" name="amount" value="{{ $expense->amount }}" class="border p-2 w-full" required>
    </div>
    <div>
        <label>Date</label>
        <input type="date" name="date" value="{{ $expense->date }}" class="border p-2 w-full" required>
    </div>
    <div>
        <label>Description</label>
        <textarea name="description" class="border p-2 w-full">{{ $expense->description }}</textarea>
    </div>
    <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">Update</button>
</form>
@endsection

@extends('layouts.app')
@section('title','Edit Expense')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <h1 class="text-2xl font-semibold mb-4">Edit Expense #{{ $expense->id }}</h1>

    <form action="{{ route('expenses.update', $expense) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @method('PUT')
        @include('expenses._form')
        <div class="mt-4 flex gap-2">
            <a href="{{ route('expenses.index') }}" class="px-4 py-2 border rounded">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Update Expense</button>
        </div>
    </form>
</div>
@endsection

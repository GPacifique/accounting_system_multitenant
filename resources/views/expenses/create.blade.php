@extends('layouts.app')
@section('title','New Expense')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <h1 class="text-2xl font-semibold mb-4">Create Expense</h1>

    <form action="{{ route('expenses.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @include('expenses._form')
        <div class="mt-4 flex gap-2">
            <a href="{{ route('expenses.index') }}" class="px-4 py-2 border rounded">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save Expense</button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Generate Report</h1>

<form action="{{ route('reports.store') }}" method="POST" class="space-y-4">
    @csrf
    <div>
        <label>Title</label>
        <input type="text" name="title" class="border p-2 w-full" required>
    </div>
    <div>
        <label>Type</label>
        <select name="type" class="border p-2 w-full" required>
            <option value="expense">Expense</option>
            <option value="employee">Employee</option>
            <option value="project">Project</option>
        </select>
    </div>
    <div>
        <label>Date</label>
        <input type="date" name="date" class="border p-2 w-full" required>
    </div>
    <div>
        <label>Description</label>
        <textarea name="description" class="border p-2 w-full"></textarea>
    </div>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Generate</button>
</form>
@endsection

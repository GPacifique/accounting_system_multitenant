@extends('layouts.app')

@section('title', 'New Report')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <h1 class="text-2xl font-semibold mb-6">Create New Report</h1>

    <form action="{{ route('reports.store') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2">
            @error('title')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Type</label>
            <input type="text" name="type" value="{{ old('type') }}" class="w-full border rounded px-3 py-2">
            @error('type')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Report Date</label>
            <input type="date" name="report_date" value="{{ old('report_date') }}" class="w-full border rounded px-3 py-2">
            @error('report_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="4" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save Report</button>
    </form>
</div>
@endsection

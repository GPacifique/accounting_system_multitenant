@extends('layouts.app')

@section('title', 'Edit Report')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Edit Report</h1>
        <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Back to Reports</a>
    </div>

    <form action="{{ route('reports.update', $report) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Title</label>
            <input type="text" name="title" value="{{ old('title', $report->title) }}" class="w-full border rounded px-3 py-2">
            @error('title')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Type</label>
            <input type="text" name="type" value="{{ old('type', $report->type) }}" class="w-full border rounded px-3 py-2">
            @error('type')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Report Date</label>
            <input type="date" name="report_date" value="{{ old('report_date', $report->report_date->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2">
            @error('report_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="4" class="w-full border rounded px-3 py-2">{{ old('description', $report->description) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-1">Report Data (JSON)</label>
            <textarea name="data" rows="4" class="w-full border rounded px-3 py-2">{{ old('data', json_encode($report->data ?? [], JSON_PRETTY_PRINT)) }}</textarea>
            <p class="text-sm text-gray-400 mt-1">Optional: Provide a JSON object for chart data, e.g. <code>{"Jan":100,"Feb":150}</code></p>
        </div>

        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Update Report</button>
    </form>
</div>
@endsection

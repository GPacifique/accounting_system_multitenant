@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-3xl">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">{{ $project->name }}</h1>
        <div>
            <a href="{{ route('projects.edit', $project) }}" class="px-3 py-1 border rounded mr-2">Edit</a>
            <a href="{{ route('projects.index') }}" class="px-3 py-1 border rounded">Back</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <p class="mb-4">{{ $project->description ?? '—' }}</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700">
            <div><strong>Status:</strong> {{ ucfirst($project->status) }}</div>
            <div><strong>Start:</strong> {{ $project->start_date?->format('Y-m-d') ?? '-' }}</div>
            <div><strong>End:</strong> {{ $project->end_date?->format('Y-m-d') ?? '-' }}</div>
            <div class="md:col-span-3"><strong>Budget:</strong> {{ number_format($project->budget ?? 0, 2) }}</div>
        </div>
    </div>
</div>
@endsection

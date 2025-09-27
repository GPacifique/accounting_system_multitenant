{{-- resources/views/projects/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Projects</h1>

        <div class="flex items-center gap-3">
            <a href="{{ route('projects.create') }}" class="btn bg-blue-600 hover:bg-blue-700 inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                <span>
                    New Project
                </span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($projects->isEmpty())
        <div class="p-6 bg-yellow-50 border border-yellow-200 rounded">
            No projects found.
        </div>
    @else
        <div class="overflow-x-auto bg-white rounded shadow-sm">
            <table class="w-full table-auto">
                <thead>
                    <tr class="text-left bg-gray-50">
                        <th class="px-4 py-3 text-sm font-medium">#</th>
                        <th class="px-4 py-3 text-sm font-medium">Name</th>
                        <th class="px-4 py-3 text-sm font-medium">Client</th>
                        <th class="px-4 py-3 text-sm font-medium">Start</th>
                        <th class="px-4 py-3 text-sm font-medium">End</th>
                        <th class="px-4 py-3 text-sm font-medium">Contract Value</th>
                        <th class="px-4 py-3 text-sm font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $project)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $project->id }}</td>
                            <td class="px-4 py-3 text-sm">{{ $project->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $project->client->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($project->start_date)
                                    {{ \Illuminate\Support\Carbon::parse($project->start_date)->format('Y-m-d') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($project->end_date)
                                    {{ \Illuminate\Support\Carbon::parse($project->end_date)->format('Y-m-d') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ number_format($project->contract_value ?? 0, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:underline mr-2">View</a>
                                <a href="{{ route('projects.edit', $project) }}" class="text-yellow-600 hover:underline mr-2">Edit</a>

                                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this project?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination: only show if paginator methods exist --}}
        @if(method_exists($projects, 'links'))
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Showing {{ $projects->firstItem() ?? 1 }} - {{ $projects->lastItem() ?? $projects->count() }} of {{ $projects->total() ?? $projects->count() }}
                </div>

                <div>
                    {{-- preserve query string in pagination links if available --}}
                    {{ method_exists($projects, 'withQueryString') ? $projects->withQueryString()->links() : $projects->links() }}
                </div>
            </div>
        @endif
    @endif
</div>
@endsection

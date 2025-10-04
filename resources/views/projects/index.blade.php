{{-- resources/views/projects/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Projects')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="max-w-6xl mx-auto p-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold">Projects</h1>
            <p class="text-sm text-gray-500 mt-1">Track projects, budgets and timelines</p>
        </div>

        <div class="flex items-center gap-3">
            <form id="projectsSearchForm" action="{{ route('projects.index') }}" method="GET" class="flex items-center gap-2">
                <label for="q" class="sr-only">Search projects</label>
                <div class="relative">
                    <input id="q" name="q" type="search" value="{{ request('q') ?? '' }}"
                        placeholder="Search name, client, or date..."
                        class="pl-10 pr-4 py-2 rounded-lg border bg-white shadow-sm focus:ring-2 focus:ring-indigo-200 focus:outline-none w-64"
                        autocomplete="off" aria-label="Search projects">
                    <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/></svg>
                </div>

                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm">Search</button>
                <a href="{{ route('projects.index') }}" class="px-3 py-2 border rounded-lg text-gray-600 hover:bg-gray-50">Reset</a>
            </form>

            <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                New Project
            </a>

            <button id="exportCsvBtn" class="px-3 py-2 border rounded-lg text-gray-700 hover:bg-gray-50" title="Export visible projects to CSV">Export CSV</button>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Empty state --}}
    @if($projects->isEmpty())
        <div class="p-6 bg-yellow-50 border border-yellow-200 rounded text-center">
            <p class="text-gray-700">No projects found.</p>
            <a href="{{ route('projects.create') }}" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded">Create a project</a>
        </div>
    @else
        {{-- Table --}}
        <div class="overflow-x-auto bg-white rounded shadow-sm border">
            <table class="w-full table-auto min-w-[900px]">
                <thead>
                    <tr class="text-left bg-gray-50 text-sm">
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Start</th>
                        <th class="px-4 py-3">End</th>
                        <th class="px-4 py-3 text-right">Contract Value</th>
                        <th class="px-4 py-3" > Amount paid</th>
                        <th class="px-4 py-3">Remaining</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Notes</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($projects as $project)
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-sm">{{ $project->id }}</td>

                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $project->name }}</div>
                                @if(!empty($project->code))
                                    <div class="text-xs text-gray-400">{{ $project->code }}</div>
                                @endif
                            </td>

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

                            <td class="px-4 py-3 text-sm text-right font-medium">
                                {{ number_format($project->contract_value ?? 0, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-medium">
                                {{ number_format($project->amount_paid ?? 0, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-right font-medium">
                                {{ number_format($project->amount_remaining ?? 0, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($project->status)
                                    <span class="px-2 py-1 text-xs rounded-full {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : ($project->status === 'completed' ? 'bg-blue-100 text-blue-800' : ($project->status === 'on-hold' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($project->notes)
                                    <span class="text-gray-700">{{ Str::limit($project->notes, 50) }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-right text-sm">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:underline">View</a>
                                    <a href="{{ route('projects.edit', $project) }}" class="text-yellow-600 hover:underline">Edit</a>

                                    <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline-block delete-project-form" data-name="{{ $project->name }}" onsubmit="return false;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-red-600 hover:underline btn-delete">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($projects, 'links'))
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Showing {{ $projects->firstItem() ?? 1 }} - {{ $projects->lastItem() ?? $projects->count() }} of {{ $projects->total() ?? $projects->count() }}
                </div>

                <div>
                    {{ method_exists($projects, 'withQueryString') ? $projects->withQueryString()->links() : $projects->links() }}
                </div>
            </div>
        @endif
    @endif
</div>
@endsection

@push('styles')
<style>
    /* Subtle polish to complement Tailwind */
    .shadow-sm { box-shadow: 0 6px 18px rgba(20,24,40,0.06); }
    .border { border: 1px solid rgba(17,24,39,0.04); }
    .min-w-\[900px\] { min-width: 900px; } /* fallback for some Tailwind setups */
    .btn-delete { cursor: pointer; }
    @media (max-width: 640px) {
        #q { width: 100% !important; }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Debounced search: submit when cleared automatically
    (function () {
        const input = document.getElementById('q');
        if (!input) return;
        let timer = null;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(() => {
                if (input.value.trim() === '') {
                    document.getElementById('projectsSearchForm').submit();
                }
            }, 650);
        });
    })();

    // Delegate delete confirmation for projects
    document.querySelectorAll('.delete-project-form').forEach(form => {
        const btn = form.querySelector('.btn-delete');
        if (!btn) return;
        btn.addEventListener('click', function () {
            const name = form.dataset.name || 'this project';
            if (!confirm(`Delete "${name}"? This action cannot be undone.`)) return;
            // remove the inline protection and submit
            form.onsubmit = null;
            form.submit();
        });
    });

    // Export visible rows to CSV
    document.getElementById('exportCsvBtn')?.addEventListener('click', function () {
        const rows = Array.from(document.querySelectorAll('table tbody tr'));
        if (!rows.length) { alert('No projects to export'); return; }

        const data = [['ID','Name','Client','Start','End','Contract Value']];

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length < 6) return;
            const id = cells[0].innerText.trim();
            const name = cells[1].querySelector('.font-medium')?.innerText.trim() ?? cells[1].innerText.trim();
            const client = cells[2].innerText.trim();
            const start = cells[3].innerText.trim();
            const end = cells[4].innerText.trim();
            const value = cells[5].innerText.trim();
            data.push([id, name, client, start, end, value]);
        });

        const csv = data.map(r => r.map(c => `"${String(c).replace(/"/g,'""')}"`).join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `projects-${new Date().toISOString().slice(0,10)}.csv`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    });
});
</script>
@endpush

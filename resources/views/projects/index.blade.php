{{-- resources/views/projects/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Construction Project Management - Track Progress & Payments | SiteLedger')
@section('meta_description', 'Comprehensive construction project management system. Track project budgets, monitor progress, manage timelines, and oversee payments. Complete project portfolio management for construction companies.')
@section('meta_keywords', 'construction project management, project portfolio, budget tracking, project progress, timeline management, construction project oversight, project payments')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="py-6">
    {{-- Role Check: Admin or Manager Only --}}
    @unless(auth()->user()->hasAnyRole(['admin', 'manager']))
        <x-enhanced-alert type="danger">
            <i class="fas fa-exclamation-triangle"></i> You do not have permission to access this page.
        </x-enhanced-alert>
        @php
            abort(403, 'Unauthorized access');
        @endphp
    @endunless

    {{-- Page Header --}}
    <x-page-header 
        title="Projects" 
        subtitle="Track projects, budgets and timelines">
        
        <x-slot name="actions">
            <div class="d-flex gap-2">
                {{-- Download Buttons --}}
                <x-download-buttons 
                    route="projects.export" 
                    filename="projects" 
                    size="sm" />
                
                @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                    <x-enhanced-button type="success" icon="fas fa-plus" href="{{ route('projects.create') }}">
                        New Project
                    </x-enhanced-button>
                @endif
            </div>
            <x-enhanced-button type="secondary" icon="fas fa-download" id="exportCsvBtn">
                Export CSV
            </x-enhanced-button>
        </x-slot>
    </x-page-header>

    {{-- Search Bar --}}
    <div class="mb-6">
        <form id="projectsSearchForm" action="{{ route('projects.index') }}" method="GET" class="flex items-center gap-3">
            <label for="q" class="sr-only">Search projects</label>
            <div class="relative flex-1 max-w-md">
                <input id="q" name="q" type="search" value="{{ request('q') ?? '' }}"
                    placeholder="Search name, client, or date..."
                    class="form-input-enhanced pl-10"
                    autocomplete="off" aria-label="Search projects">
                <i class="fas fa-search absolute left-3 top-4 theme-aware-text-muted"></i>
            </div>

            <x-enhanced-button type="primary" icon="fas fa-search">
                Search
            </x-enhanced-button>
            <x-enhanced-button type="secondary" href="{{ route('projects.index') }}">
                Reset
            </x-enhanced-button>
        </form>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <x-enhanced-alert type="success" dismissible>
            {{ session('success') }}
        </x-enhanced-alert>
    @endif

    {{-- Projects Table --}}
    <x-enhanced-card title="All Projects" subtitle="List of active and completed projects">
        @if($projects->isEmpty())
            <div class="empty-state-enhanced">
                <div class="empty-state-icon">📁</div>
                <h3 class="empty-state-title">No projects found</h3>
                <p class="empty-state-description">Start by creating your first project</p>
                @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                    <x-enhanced-button type="primary" icon="fas fa-plus" href="{{ route('projects.create') }}" class="mt-4">
                        Create Project
                    </x-enhanced-button>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="enhanced-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Client</th>
                            <th>Start</th>
                            <th>End</th>
                            <th class="text-right">Contract Value</th>
                            <th>Amount Paid</th>
                            <th>Remaining</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>{{ $project->id }}</td>

                                <td>
                                    <div class="font-medium">{{ $project->name }}</div>
                                    @if(!empty($project->code))
                                        <div class="text-xs theme-aware-text-muted">{{ $project->code }}</div>
                                    @endif
                                </td>

                                <td>{{ $project->client->name ?? '—' }}</td>

                                <td>
                                    @if($project->start_date)
                                        {{ \Illuminate\Support\Carbon::parse($project->start_date)->format('Y-m-d') }}
                                    @else
                                        —
                                    @endif
                                </td>

                                <td>
                                    @if($project->end_date)
                                        {{ \Illuminate\Support\Carbon::parse($project->end_date)->format('Y-m-d') }}
                                    @else
                                        —
                                    @endif
                                </td>

                                <td class="text-right font-medium">
                                    {{ number_format($project->contract_value ?? 0, 2) }}
                                </td>
                                <td class="text-right font-medium">
                                    {{ number_format($project->amount_paid ?? 0, 2) }}
                                </td>
                                <td class="text-right font-medium">
                                    {{ number_format($project->amount_remaining ?? 0, 2) }}
                                </td>
                                <td>
                                    @if($project->status)
                                        <span class="badge-enhanced {{ $project->status === 'active' ? 'badge-success' : ($project->status === 'completed' ? 'badge-info' : ($project->status === 'on-hold' ? 'badge-warning' : 'badge-secondary')) }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                    @else
                                        <span class="theme-aware-text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($project->notes)
                                        <span class="text-gray-700">{{ Str::limit($project->notes, 50) }}</span>
                                    @else
                                        <span class="theme-aware-text-muted">—</span>
                                    @endif
                                </td>

                                <td class="text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <x-enhanced-button type="info" size="sm" href="{{ route('projects.show', $project) }}">
                                            <i class="fas fa-eye"></i>
                                        </x-enhanced-button>
                                        <x-enhanced-button type="warning" size="sm" href="{{ route('projects.edit', $project) }}">
                                            <i class="fas fa-edit"></i>
                                        </x-enhanced-button>

                                        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline-block delete-project-form" data-name="{{ $project->name }}" onsubmit="return false;">
                                            @csrf
                                            @method('DELETE')
                                            <x-enhanced-button type="danger" size="sm" class="btn-delete">
                                                <i class="fas fa-trash"></i>
                                            </x-enhanced-button>
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
                <div class="mt-6 flex items-center justify-between px-4">
                    <div class="text-sm theme-aware-text-secondary">
                        Showing {{ $projects->firstItem() ?? 1 }} - {{ $projects->lastItem() ?? $projects->count() }} of {{ $projects->total() ?? $projects->count() }}
                    </div>

                    <div>
                        {{ method_exists($projects, 'withQueryString') ? $projects->withQueryString()->links() : $projects->links() }}
                    </div>
            </div>
        @endif
        @endif
    </x-enhanced-card>
</div>
@endsection

@push('styles')
<style>
    /* Additional polish */
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

@extends('layouts.app')

@section('title', 'Tasks Management - Project Task Tracking | SiteLedger')
@section('meta_description', 'Manage and track project tasks with priorities, assignments, and deadlines. Comprehensive task management for construction projects.')
@section('meta_keywords', 'task management, project tasks, task tracking, construction management, task assignment, deadline tracking')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold leading-tight theme-aware-text">Tasks</h1>
            <p class="text-sm theme-aware-text-muted mt-1">Manage project tasks and assignments</p>
        </div>
        <div class="flex items-center gap-3">
            @can('tasks.create')
                <a href="{{ route('tasks.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Task
                </a>
            @endcan
            
            {{-- Export Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="inline-flex items-center px-4 py-2 theme-aware-bg-secondary theme-aware-text-secondary text-sm font-medium rounded-lg hover:theme-aware-bg-tertiary focus:ring-4 focus:ring-gray-300 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute right-0 mt-2 w-48 theme-aware-bg-card rounded-md theme-aware-shadow z-50 theme-aware-border border">
                    <div class="py-1">
                        <a href="{{ route('tasks.export.csv') }}" 
                           class="block px-4 py-2 text-sm theme-aware-text hover:theme-aware-bg-secondary">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export as CSV
                        </a>
                        <a href="{{ route('tasks.export.pdf') }}" 
                           class="block px-4 py-2 text-sm theme-aware-text hover:theme-aware-bg-secondary">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Export as PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="theme-aware-bg-card rounded-lg p-4 border">
            <div class="text-2xl font-bold text-blue-600">{{ number_format($taskStats['total']) }}</div>
            <div class="text-sm theme-aware-text-muted">Total Tasks</div>
        </div>

        <div class="theme-aware-bg-card rounded-lg p-4 border">
            <div class="text-2xl font-bold theme-aware-text-secondary">{{ number_format($taskStats['pending']) }}</div>
            <div class="text-sm theme-aware-text-muted">Pending</div>
        </div>

        <div class="theme-aware-bg-card rounded-lg p-4 border">
            <div class="text-2xl font-bold text-blue-600">{{ number_format($taskStats['in_progress']) }}</div>
            <div class="text-sm theme-aware-text-muted">In Progress</div>
        </div>

        <div class="theme-aware-bg-card rounded-lg p-4 border">
            <div class="text-2xl font-bold text-green-600">{{ number_format($taskStats['completed']) }}</div>
            <div class="text-sm theme-aware-text-muted">Completed</div>
        </div>

        <div class="theme-aware-bg-card rounded-lg p-4 border">
            <div class="text-2xl font-bold text-red-600">{{ number_format($taskStats['overdue']) }}</div>
            <div class="text-sm theme-aware-text-muted">Overdue</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border mb-6">
        <form method="GET" action="{{ route('tasks.index') }}" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
            {{-- Search --}}
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium theme-aware-text-secondary mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Search tasks..."
                       class="w-full px-3 py-2 border theme-aware-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-blue-500">
            </div>

            {{-- Status Filter --}}
            <div>
                <label for="status" class="block text-sm font-medium theme-aware-text-secondary mb-1">Status</label>
                <select name="status" id="status" class="px-3 py-2 border theme-aware-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-blue-500">
                    <option value="">All Statuses</option>
                    @foreach(\App\Models\Task::STATUSES as $key => $value)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Priority Filter --}}
            <div>
                <label for="priority" class="block text-sm font-medium theme-aware-text-secondary mb-1">Priority</label>
                <select name="priority" id="priority" class="px-3 py-2 border theme-aware-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-blue-500">
                    <option value="">All Priorities</option>
                    @foreach(\App\Models\Task::PRIORITIES as $key => $value)
                        <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Project Filter --}}
            <div>
                <label for="project_id" class="block text-sm font-medium theme-aware-text-secondary mb-1">Project</label>
                <select name="project_id" id="project_id" class="px-3 py-2 border theme-aware-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-blue-500">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Assigned To Filter --}}
            <div>
                <label for="assigned_to" class="block text-sm font-medium theme-aware-text-secondary mb-1">Assigned To</label>
                <select name="assigned_to" id="assigned_to" class="px-3 py-2 border theme-aware-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-blue-500">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Actions --}}
            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors">
                    Filter
                </button>
                <a href="{{ route('tasks.index') }}" class="px-4 py-2 theme-aware-bg-secondary theme-aware-text-secondary text-sm font-medium rounded-md hover:theme-aware-bg-tertiary focus:ring-4 focus:ring-gray-300 transition-colors">
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Tasks Table --}}
    <div class="theme-aware-bg-card rounded-lg shadow-sm border overflow-hidden">
        @if($tasks->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 theme-aware-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium theme-aware-text">No tasks found</h3>
                <p class="mt-1 text-sm theme-aware-text-muted">Get started by creating a new task.</p>
                @can('tasks.create')
                    <div class="mt-6">
                        <a href="{{ route('tasks.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            New Task
                        </a>
                    </div>
                @endcan
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="theme-aware-bg-secondary border-b">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium theme-aware-text-muted uppercase tracking-wider">Task</th>
                            <th class="px-6 py-3 text-xs font-medium theme-aware-text-muted uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-xs font-medium theme-aware-text-muted uppercase tracking-wider">Assigned To</th>
                            <th class="px-6 py-3 text-xs font-medium theme-aware-text-muted uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-xs font-medium theme-aware-text-muted uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-xs font-medium theme-aware-text-muted uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-xs font-medium theme-aware-text-muted uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="theme-aware-bg-card divide-y theme-aware-border">
                        @foreach($tasks as $task)
                            <tr class="hover:theme-aware-bg-secondary transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="font-medium theme-aware-text">{{ $task->title }}</div>
                                        @if($task->description)
                                            <div class="text-sm theme-aware-text-muted">{{ Str::limit($task->description, 60) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($task->project)
                                        <a href="{{ route('projects.show', $task->project) }}" class="text-blue-600 hover:text-blue-900 hover:underline">
                                            {{ $task->project->name }}
                                        </a>
                                    @else
                                        <span class="theme-aware-text-muted">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($task->assignedTo)
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 theme-aware-bg-tertiary rounded-full flex items-center justify-center mr-2">
                                                <span class="text-xs font-medium theme-aware-text-secondary">
                                                    {{ substr($task->assignedTo->name, 0, 2) }}
                                                </span>
                                            </div>
                                            {{ $task->assignedTo->name }}
                                        </div>
                                    @else
                                        <span class="theme-aware-text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $task->priority_color }}">
                                        {{ \App\Models\Task::PRIORITIES[$task->priority] ?? $task->priority }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $task->status_color }}">
                                        {{ \App\Models\Task::STATUSES[$task->status] ?? $task->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($task->due_date)
                                        <div class="{{ $task->is_overdue ? 'text-red-600 font-medium' : 'theme-aware-text-secondary' }}">
                                            {{ $task->due_date->format('M d, Y') }}
                                            @if($task->is_overdue)
                                                <span class="text-xs">(Overdue)</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="theme-aware-text-muted">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('tasks.show', $task) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>

                                        @can('tasks.edit')
                                            <a href="{{ route('tasks.edit', $task) }}" 
                                               class="theme-aware-text-secondary hover:theme-aware-text" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                        @endcan

                                        @can('tasks.delete')
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($tasks->hasPages())
                <div class="px-6 py-4 border-t theme-aware-border">
                    {{ $tasks->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
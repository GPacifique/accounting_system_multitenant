@extends('layouts.app')

@section('title', $task->title . ' - Task Details | SiteLedger')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold leading-tight theme-aware-text">{{ $task->title }}</h1>
            <p class="text-sm theme-aware-text-muted mt-1">Task Details</p>
        </div>
        <div class="flex items-center gap-3">
            @can('tasks.edit')
                <a href="{{ route('tasks.edit', $task) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Task
                </a>
            @endcan
            
            <a href="{{ route('tasks.index') }}" 
               class="inline-flex items-center px-4 py-2 theme-aware-bg-secondary theme-aware-text-secondary text-sm font-medium rounded-lg hover:theme-aware-bg-tertiary focus:ring-4 focus:ring-gray-300 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Tasks
            </a>
        </div>
    </div>

    {{-- Task Overview --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Status Card --}}
        <div class="theme-aware-bg-card rounded-lg p-6 border">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium theme-aware-text">Status</h3>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $task->status_color }}">
                    {{ \App\Models\Task::STATUSES[$task->status] ?? $task->status }}
                </span>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm theme-aware-text-muted">Priority:</span>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $task->priority_color }}">
                        {{ \App\Models\Task::PRIORITIES[$task->priority] ?? $task->priority }}
                    </span>
                </div>
                @if($task->due_date)
                    <div class="flex justify-between">
                        <span class="text-sm theme-aware-text-muted">Due Date:</span>
                        <span class="text-sm {{ $task->is_overdue ? 'text-red-600 font-medium' : 'theme-aware-text' }}">
                            {{ $task->due_date->format('M d, Y') }}
                            @if($task->is_overdue)
                                <span class="text-xs">(Overdue)</span>
                            @endif
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Assignment Card --}}
        <div class="theme-aware-bg-card rounded-lg p-6 border">
            <h3 class="text-lg font-medium theme-aware-text mb-4">Assignment</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm theme-aware-text-muted">Assigned To:</span>
                    <div class="mt-1">
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
                    </div>
                </div>
                
                <div>
                    <span class="text-sm theme-aware-text-muted">Created By:</span>
                    <div class="mt-1">
                        @if($task->createdBy)
                            <div class="flex items-center">
                                <div class="w-8 h-8 theme-aware-bg-tertiary rounded-full flex items-center justify-center mr-2">
                                    <span class="text-xs font-medium theme-aware-text-secondary">
                                        {{ substr($task->createdBy->name, 0, 2) }}
                                    </span>
                                </div>
                                {{ $task->createdBy->name }}
                            </div>
                        @else
                            <span class="theme-aware-text-muted">Unknown</span>
                        @endif
                    </div>
                </div>

                @if($task->project)
                    <div>
                        <span class="text-sm theme-aware-text-muted">Project:</span>
                        <div class="mt-1">
                            <a href="{{ route('projects.show', $task->project) }}" 
                               class="text-blue-600 hover:text-blue-900 hover:underline">
                                {{ $task->project->name }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Time & Cost Card --}}
        <div class="theme-aware-bg-card rounded-lg p-6 border">
            <h3 class="text-lg font-medium theme-aware-text mb-4">Time & Cost</h3>
            <div class="space-y-3">
                @if($task->estimated_hours || $task->actual_hours)
                    <div>
                        <span class="text-sm theme-aware-text-muted">Time (Hours):</span>
                        <div class="mt-1 text-sm">
                            <div class="flex justify-between">
                                <span>Estimated:</span>
                                <span>{{ $task->estimated_hours ?? 0 }}</span>
                            </div>
                            @if($task->actual_hours)
                                <div class="flex justify-between">
                                    <span>Actual:</span>
                                    <span>{{ $task->actual_hours }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($task->estimated_cost || $task->actual_cost)
                    <div>
                        <span class="text-sm theme-aware-text-muted">Cost (RWF):</span>
                        <div class="mt-1 text-sm">
                            <div class="flex justify-between">
                                <span>Estimated:</span>
                                <span>{{ number_format($task->estimated_cost ?? 0, 0) }}</span>
                            </div>
                            @if($task->actual_cost)
                                <div class="flex justify-between">
                                    <span>Actual:</span>
                                    <span>{{ number_format($task->actual_cost, 0) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($task->start_date || $task->completed_date)
                    <div>
                        <span class="text-sm theme-aware-text-muted">Dates:</span>
                        <div class="mt-1 text-sm">
                            @if($task->start_date)
                                <div class="flex justify-between">
                                    <span>Started:</span>
                                    <span>{{ $task->start_date->format('M d, Y') }}</span>
                                </div>
                            @endif
                            @if($task->completed_date)
                                <div class="flex justify-between">
                                    <span>Completed:</span>
                                    <span>{{ $task->completed_date->format('M d, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Description and Details --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Description --}}
        <div class="theme-aware-bg-card rounded-lg p-6 border">
            <h3 class="text-lg font-medium theme-aware-text mb-4">Description</h3>
            @if($task->description)
                <div class="prose prose-sm max-w-none theme-aware-text">
                    {!! nl2br(e($task->description)) !!}
                </div>
            @else
                <p class="theme-aware-text-muted italic">No description provided.</p>
            @endif
        </div>

        {{-- Notes --}}
        <div class="theme-aware-bg-card rounded-lg p-6 border">
            <h3 class="text-lg font-medium theme-aware-text mb-4">Notes</h3>
            @if($task->notes)
                <div class="prose prose-sm max-w-none theme-aware-text">
                    {!! nl2br(e($task->notes)) !!}
                </div>
            @else
                <p class="theme-aware-text-muted italic">No notes available.</p>
            @endif
        </div>
    </div>

    {{-- Task Metadata --}}
    <div class="mt-6 theme-aware-bg-card rounded-lg p-6 border">
        <h3 class="text-lg font-medium theme-aware-text mb-4">Task Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="theme-aware-text-muted">Created:</span>
                <div class="font-medium">{{ $task->created_at->format('M d, Y \a\t g:i A') }}</div>
            </div>
            <div>
                <span class="theme-aware-text-muted">Last Updated:</span>
                <div class="font-medium">{{ $task->updated_at->format('M d, Y \a\t g:i A') }}</div>
            </div>
            <div>
                <span class="theme-aware-text-muted">Task ID:</span>
                <div class="font-medium">#{{ $task->id }}</div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    @can('tasks.delete')
        <div class="mt-6 pt-6 border-t theme-aware-border">
            <div class="flex justify-end">
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this task? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Task
                    </button>
                </form>
            </div>
        </div>
    @endcan
</div>
@endsection
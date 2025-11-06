@extends('layouts.app')

@section('title', 'Edit Task - ' . $task->title . ' | SiteLedger')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold leading-tight theme-aware-text">Edit Task</h1>
            <p class="text-sm theme-aware-text-muted mt-1">{{ $task->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('tasks.show', $task) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:ring-4 focus:ring-gray-300 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View Task
            </a>
            <a href="{{ route('tasks.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:ring-4 focus:ring-gray-300 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Tasks
            </a>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="theme-aware-bg-card rounded-lg shadow-sm border">
        <form action="{{ route('tasks.update', $task) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Basic Information --}}
            <div class="space-y-4">
                <h3 class="text-lg font-medium theme-aware-text border-b pb-2">Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Title --}}
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Task Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               value="{{ old('title', $task->title) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                               placeholder="Enter task title..."
                               required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Project --}}
                    <div>
                        <label for="project_id" class="block text-sm font-medium theme-aware-text-secondary mb-2">Project</label>
                        <select name="project_id" 
                                id="project_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('project_id') border-red-500 @enderror">
                            <option value="">Select Project (Optional)</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Assigned To --}}
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium theme-aware-text-secondary mb-2">Assigned To</label>
                        <select name="assigned_to" 
                                id="assigned_to" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('assigned_to') border-red-500 @enderror">
                            <option value="">Select User (Optional)</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Priority --}}
                    <div>
                        <label for="priority" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Priority <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" 
                                id="priority" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('priority') border-red-500 @enderror"
                                required>
                            <option value="">Select Priority</option>
                            @foreach(\App\Models\Task::PRIORITIES as $key => $value)
                                <option value="{{ $key }}" {{ old('priority', $task->priority) == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium theme-aware-text-secondary mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" 
                                id="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror"
                                required>
                            @foreach(\App\Models\Task::STATUSES as $key => $value)
                                <option value="{{ $key }}" {{ old('status', $task->status) == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium theme-aware-text-secondary mb-2">Description</label>
                    <textarea name="description" 
                              id="description" 
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Enter task description...">{{ old('description', $task->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Dates and Time --}}
            <div class="space-y-4">
                <h3 class="text-lg font-medium theme-aware-text border-b pb-2">Dates & Time</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Start Date --}}
                    <div>
                        <label for="start_date" class="block text-sm font-medium theme-aware-text-secondary mb-2">Start Date</label>
                        <input type="date" 
                               name="start_date" 
                               id="start_date" 
                               value="{{ old('start_date', $task->start_date ? $task->start_date->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Due Date --}}
                    <div>
                        <label for="due_date" class="block text-sm font-medium theme-aware-text-secondary mb-2">Due Date</label>
                        <input type="date" 
                               name="due_date" 
                               id="due_date" 
                               value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('due_date') border-red-500 @enderror">
                        @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Completed Date --}}
                    <div>
                        <label for="completed_date" class="block text-sm font-medium theme-aware-text-secondary mb-2">Completed Date</label>
                        <input type="date" 
                               name="completed_date" 
                               id="completed_date" 
                               value="{{ old('completed_date', $task->completed_date ? $task->completed_date->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('completed_date') border-red-500 @enderror">
                        @error('completed_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Time and Cost Tracking --}}
            <div class="space-y-4">
                <h3 class="text-lg font-medium theme-aware-text border-b pb-2">Time & Cost Tracking</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Time Tracking --}}
                    <div class="space-y-4">
                        <h4 class="font-medium theme-aware-text">Time (Hours)</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="estimated_hours" class="block text-sm font-medium theme-aware-text-secondary mb-2">Estimated</label>
                                <input type="number" 
                                       name="estimated_hours" 
                                       id="estimated_hours" 
                                       min="0"
                                       value="{{ old('estimated_hours', $task->estimated_hours) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('estimated_hours') border-red-500 @enderror"
                                       placeholder="0">
                                @error('estimated_hours')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="actual_hours" class="block text-sm font-medium theme-aware-text-secondary mb-2">Actual</label>
                                <input type="number" 
                                       name="actual_hours" 
                                       id="actual_hours" 
                                       min="0"
                                       value="{{ old('actual_hours', $task->actual_hours) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('actual_hours') border-red-500 @enderror"
                                       placeholder="0">
                                @error('actual_hours')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Cost Tracking --}}
                    <div class="space-y-4">
                        <h4 class="font-medium theme-aware-text">Cost (RWF)</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="estimated_cost" class="block text-sm font-medium theme-aware-text-secondary mb-2">Estimated</label>
                                <input type="number" 
                                       name="estimated_cost" 
                                       id="estimated_cost" 
                                       min="0"
                                       step="0.01"
                                       value="{{ old('estimated_cost', $task->estimated_cost) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('estimated_cost') border-red-500 @enderror"
                                       placeholder="0.00">
                                @error('estimated_cost')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="actual_cost" class="block text-sm font-medium theme-aware-text-secondary mb-2">Actual</label>
                                <input type="number" 
                                       name="actual_cost" 
                                       id="actual_cost" 
                                       min="0"
                                       step="0.01"
                                       value="{{ old('actual_cost', $task->actual_cost) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('actual_cost') border-red-500 @enderror"
                                       placeholder="0.00">
                                @error('actual_cost')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="space-y-4">
                <h3 class="text-lg font-medium theme-aware-text border-b pb-2">Additional Information</h3>
                
                <div>
                    <label for="notes" class="block text-sm font-medium theme-aware-text-secondary mb-2">Notes</label>
                    <textarea name="notes" 
                              id="notes" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror"
                              placeholder="Any additional notes or comments...">{{ old('notes', $task->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('tasks.show', $task) }}" 
                   class="px-6 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 focus:ring-4 focus:ring-gray-300 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors">
                    Update Task
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
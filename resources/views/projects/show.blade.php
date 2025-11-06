@extends('layouts.app')
@section('title', 'Project Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold theme-aware-text leading-tight">{{ $project->name }}</h1>
                <p class="text-sm theme-aware-text-muted mt-1">Status: <span class="font-medium theme-aware-text-secondary">{{ ucfirst($project->status ?? '—') }}</span></p>
            </div>
            <div class="flex items-center gap-2 mt-4 sm:mt-0">
                <a href="{{ route('projects.edit', $project) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('projects.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Projects
                </a>
            </div>
        </div>

        <!-- Details Card -->
        <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                @if(!empty($project->description))
                    <div class="mb-6">
                        <h3 class="text-sm font-medium theme-aware-text-muted">Description</h3>
                        <p class="mt-1 theme-aware-text">{{ $project->description }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 theme-aware-text">
                    <div>
                        <h4 class="text-sm font-medium theme-aware-text-muted">Start Date</h4>
                        <p class="mt-1">{{ optional($project->start_date)->format('Y-m-d') ?? '—' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium theme-aware-text-muted">End Date</h4>
                        <p class="mt-1">{{ optional($project->end_date)->format('Y-m-d') ?? '—' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium theme-aware-text-muted">Budget</h4>
                        <p class="mt-1">{{ number_format($project->budget ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary {
        @apply inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    .btn-secondary {
        @apply inline-flex items-center px-4 py-2 theme-aware-bg-tertiary border theme-aware-border rounded-md font-semibold text-xs theme-aware-text-secondary uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:theme-aware-border-secondary focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
</style>
@endpush

@extends('layouts.app')
@section('title', 'Report Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold theme-aware-text leading-tight">{{ $report->title }}</h1>
                <p class="text-sm theme-aware-text-muted mt-1">Generated on {{ optional($report->report_date)->format('Y-m-d') }}</p>
            </div>
            <div class="flex items-center gap-2 mt-4 sm:mt-0">
                <a href="{{ route('reports.edit', $report) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('reports.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Reports
                </a>
            </div>
        </div>

        <!-- Details Card -->
        <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium theme-aware-text-muted">Type</h3>
                        <p class="mt-1 theme-aware-text">{{ $report->type }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium theme-aware-text-muted">Report Date</h3>
                        <p class="mt-1 theme-aware-text">{{ optional($report->report_date)->format('Y-m-d') }}</p>
                    </div>
                </div>

                @if($report->description)
                    <div class="mt-6">
                        <h3 class="text-sm font-medium theme-aware-text-muted">Description</h3>
                        <p class="mt-1 theme-aware-text">{{ $report->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        @if($report->data && is_array($report->data) && count($report->data))
            <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="p-6 sm:p-8">
                    <h3 class="theme-aware-text-secondary font-semibold mb-4">Report Chart</h3>
                    <div class="h-64">
                        <canvas id="reportChart"></canvas>
                    </div>
                </div>
            </div>
        @endif

        <!-- Danger Zone -->
        <div class="flex flex-wrap gap-2">
            <form action="{{ route('reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Delete this report?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">
                    <i class="fas fa-trash-alt mr-2"></i>Delete
                </button>
            </form>
        </div>
    </div>
</div>

@if($report->data && is_array($report->data) && count($report->data))
@push('scripts')
<script>
    const reportData = @json($report->data);
    const ctx = document.getElementById('reportChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(reportData),
                datasets: [{
                    label: @json($report->title),
                    data: Object.values(reportData),
                    backgroundColor: 'rgba(59,130,246,0.7)',
                    borderColor: 'rgba(59,130,246,1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });
    }
</script>
@endpush
@endif
@endsection

@push('styles')
<style>
    .btn-primary {
        @apply inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    .btn-secondary {
        @apply inline-flex items-center px-4 py-2 theme-aware-bg-tertiary border theme-aware-border rounded-md font-semibold text-xs theme-aware-text-secondary uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:theme-aware-border-secondary focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    .btn-danger {
        @apply inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
</style>
@endpush

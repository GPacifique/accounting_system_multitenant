@extends('layouts.app')

@section('title', 'Report Details')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">{{ $report->title }}</h1>
        <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Back to Reports</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h3 class="text-gray-500 font-medium">Type</h3>
                <p class="mt-1">{{ $report->type }}</p>
            </div>
            <div>
                <h3 class="text-gray-500 font-medium">Report Date</h3>
                <p class="mt-1">{{ $report->report_date->format('Y-m-d') }}</p>
            </div>
        </div>

        @if($report->description)
            <div class="mt-4">
                <h3 class="text-gray-500 font-medium">Description</h3>
                <p class="mt-1">{{ $report->description }}</p>
            </div>
        @endif
    </div>

    @if($report->data && is_array($report->data) && count($report->data))
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-gray-700 font-semibold mb-4">Report Chart</h3>
            <canvas id="reportChart" height="200"></canvas>
        </div>
    @endif

    <div class="flex gap-2">
        <a href="{{ route('reports.edit', $report) }}" class="px-4 py-2 bg-yellow-200 rounded hover:bg-yellow-300">Edit</a>
        <form action="{{ route('reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Delete this report?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-200 rounded hover:bg-red-300">Delete</button>
        </form>
    </div>
</div>

@if($report->data && is_array($report->data) && count($report->data))
<script>
    const reportData = @json($report->data);
    const ctx = document.getElementById('reportChart');

    if(ctx){
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(reportData),
                datasets: [{
                    label: '{{ $report->title }}',
                    data: Object.values(reportData),
                    backgroundColor: 'rgba(54,162,235,0.7)',
                    borderColor: 'rgba(54,162,235,1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
</script>
@endif
@endsection

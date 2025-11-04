{{-- resources/views/dashboard/manager.blade.php --}}
@extends('layouts.app')
@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('title', 'Manager Dashboard - Project Oversight & Team Management | SiteLedger')
@section('meta_description', 'Construction manager dashboard for project oversight and team management. Monitor project progress, track worker performance, manage resources, and oversee daily operations.')
@section('meta_keywords', 'manager dashboard, construction project oversight, team management, project progress tracking, worker management, construction operations')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold leading-tight">Projects & Team Dashboard</h1>
            <p class="text-sm theme-aware-text-muted mt-1">Overview of projects and team members</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 5a1 1 0 0 1 1 1v3h3a1 1 0 1 1 0 2h-3v3a1 1 0 1 1-2 0v-3H6a1 1 0 1 1 0-2h3V6a1 1 0 0 1 1-1z"/></svg>
                New Project
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border overflow-hidden transform hover:-translate-y-1 transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm theme-aware-text-muted">Total Projects</div>
                    <div class="text-2xl font-bold mt-1">{{ number_format($projectsCount) }}</div>
                    <div class="text-xs theme-aware-text-muted mt-1">This month: {{ number_format($projectsThisMonth) }}</div>
                </div>
                <div class="flex-shrink-0 text-yellow-600 bg-yellow-50 rounded-full p-3">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2l3 6 6 .5-4.5 4 1 6L12 17l-5.5 2.5 1-6L3 8.5 9 8z"/></svg>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border overflow-hidden transform hover:-translate-y-1 transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm theme-aware-text-muted">Total Budget</div>
                    <div class="text-2xl font-bold mt-1">{{ number_format($projectsTotal, 2) }}</div>
                    <div class="text-xs theme-aware-text-muted mt-1">Budget value</div>
                </div>
                <div class="flex-shrink-0 text-purple-600 bg-purple-50 rounded-full p-3">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2"/></svg>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border overflow-hidden transform hover:-translate-y-1 transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm theme-aware-text-muted">Active Team</div>
                    <div class="text-2xl font-bold mt-1">{{ number_format($activeWorkers) }}</div>
                    <div class="text-xs theme-aware-text-muted mt-1">Total: {{ number_format($totalWorkers) }}</div>
                </div>
                <div class="flex-shrink-0 text-indigo-600 bg-indigo-50 rounded-full p-3">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/></svg>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border overflow-hidden transform hover:-translate-y-1 transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm theme-aware-text-muted">Completed This Year</div>
                    <div class="text-2xl font-bold mt-1 text-green-600">0</div>
                    <div class="text-xs theme-aware-text-muted mt-1">Successful projects</div>
                </div>
                <div class="flex-shrink-0 text-green-600 bg-green-50 rounded-full p-3">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Projects Chart --}}
    <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border mb-6">
        <h3 class="text-sm font-medium text-gray-700 mb-4">Projects Budget — Last 6 months</h3>
        <div class="min-h-[260px]">
            <canvas id="projectsChart"></canvas>
        </div>
    </div>

    {{-- Project Payment Summary --}}
    <div class="mb-6 theme-aware-bg-card rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold mb-3">Project Payment Summary</h2>

        @if ($projectStats->isEmpty())
            <p class="text-sm theme-aware-text-muted">No project stats available.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-2 px-3 border-r text-sm theme-aware-text-secondary">#</th>
                            <th class="py-2 px-3 border-r text-sm theme-aware-text-secondary">Project Name</th>
                            <th class="py-2 px-3 border-r text-sm theme-aware-text-secondary">Total Amount</th>
                            <th class="py-2 px-3 border-r text-sm theme-aware-text-secondary">Amount Paid</th>
                            <th class="py-2 px-3 text-sm theme-aware-text-secondary">Amount Remaining</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projectStats as $index => $proj)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="py-2 px-3 text-sm theme-aware-text-muted">{{ $index + 1 }}</td>
                                <td class="py-2 px-3 font-medium theme-aware-text">
                                    <a href="{{ route('projects.show', $proj->id) }}" class="text-indigo-600 hover:underline">
                                        {{ $proj->project_name }}
                                    </a>
                                </td>
                                <td class="py-2 px-3 text-sm text-gray-700">
                                    <span class="inline-block px-2 py-1 rounded bg-gray-100 font-semibold">
                                        {{ number_format($proj->total_amount, 2) }}
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-sm text-green-700">
                                    <span class="inline-block px-2 py-1 rounded bg-green-50 font-semibold">
                                        {{ number_format($proj->amount_paid, 2) }}
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-sm text-red-700">
                                    <span class="inline-block px-2 py-1 rounded bg-red-50 font-semibold">
                                        {{ number_format($proj->amount_remaining, 2) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Recent Lists --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-gray-700">Recent Projects</h4>
                <a href="{{ route('projects.index') }}" class="text-indigo-600 text-sm">View all</a>
            </div>

            <ul class="divide-y">
                @forelse($recentProjects as $proj)
                    <li class="py-3 flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $proj->name ?? '—' }}</div>
                            <div class="text-xs theme-aware-text-muted">{{ optional($proj->created_at)->format('Y-m-d') ?? '—' }}</div>
                        </div>
                        <div class="text-xs px-3 py-1 rounded-full bg-yellow-50 text-yellow-700">
                            {{ number_format($proj->contract_value ?? 0, 2) }}
                        </div>
                    </li>
                @empty
                    <li class="py-6 text-center theme-aware-text-muted">No projects found.</li>
                @endforelse
            </ul>
        </div>

        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-gray-700">Team Members</h4>
                <a href="{{ route('workers.index') }}" class="text-indigo-600 text-sm">View all</a>
            </div>

            <ul class="divide-y">
                @forelse($recentWorkers as $worker)
                    <li class="py-3 flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $worker->full_name ?? ($worker->name ?? '—') }}</div>
                            <div class="text-xs theme-aware-text-muted">{{ optional($worker->created_at)->format('Y-m-d') ?? '—' }}</div>
                        </div>
                        <div class="text-xs px-3 py-1 rounded-full {{ ($worker->status ?? '') === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 theme-aware-text-secondary' }}">
                            {{ ucfirst($worker->status ?? '—') }}
                        </div>
                    </li>
                @empty
                    <li class="py-6 text-center theme-aware-text-muted">No team members found.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="mt-6 text-sm theme-aware-text-muted">© {{ date('Y') }} {{ config('app.name', 'SiteLedger') }}</div>
</div>
@endsection

@push('styles')
<style>
    .shadow-sm { box-shadow: 0 6px 18px rgba(20,24,40,0.06); }
    .border { border: 1px solid rgba(17,24,39,0.04); }
    .min-h-\[260px\] { min-height: 260px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const months = @json($months);
    const projectsData = @json($projectsMonthly);

    function createChart(canvasId, label, data, colorFrom, colorTo) {
        const el = document.getElementById(canvasId);
        if (!el) return;
        const ctx = el.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, el.height);
        gradient.addColorStop(0, colorFrom);
        gradient.addColorStop(1, colorTo);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label,
                    data,
                    backgroundColor: gradient,
                    borderColor: colorFrom,
                    borderWidth: 1,
                    borderRadius: 4,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: 'rgba(15,23,42,0.04)' } }
                }
            }
        });
    }

    createChart('projectsChart', 'Project Budget', projectsData, 'rgba(251,146,60,1)', 'rgba(251,146,60,0.08)');
});
</script>
@endpush

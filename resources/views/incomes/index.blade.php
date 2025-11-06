
@extends('layouts.app')

@section('title', 'Income Tracking - Project Payments & Revenue Management | SiteLedger')
@section('meta_description', 'Complete income and revenue management for construction projects. Track client payments, monitor project income, manage payment milestones, and analyze revenue streams across all projects.')
@section('meta_keywords', 'income tracking, revenue management, project payments, client payments, construction income, payment milestones, revenue analytics')

@vite(['resources/css/app.css', 'resources/js/app.js'])
@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Role Check: Admin or Accountant Only --}}
    @unless(auth()->user()->hasAnyRole(['admin', 'accountant']))
        <div class="p-4 mb-4 bg-red-50 border border-red-200 rounded text-red-800">
            <i class="fas fa-exclamation-triangle"></i> You do not have permission to access this page.
        </div>
        @php
            abort(403, 'Unauthorized access');
        @endphp
    @endunless

<div class="mb-6 theme-aware-bg-card rounded-lg shadow p-4">
    <h2 class="text-lg font-semibold mb-3">Project Totals</h2>

    @if($projectStats->isEmpty())
        <p class="text-sm theme-aware-text-muted">No project stats available.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border">
                <thead class="theme-aware-bg-secondary">
                    <tr>
                        <th class="py-2 px-3 border-r text-sm theme-aware-text-secondary">Project</th>
                        <th class="py-2 px-3 border-r text-sm theme-aware-text-secondary">Total Paid</th>
                        <th class="py-2 px-3 border-r text-sm theme-aware-text-secondary">Total Remaining</th>
                            <th class="py-2 px-3 text-sm theme-aware-text-secondary">Contract Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projectStats as $stat)
                        <tr class="border-t hover:theme-aware-bg-secondary">
                            <td class="py-2 px-3 font-medium">
                                {{ $stat->project_name ?? '—' }}
                            </td>
                            <td class="py-2 px-3 text-green-600 font-semibold">
                                RWF{{ number_format($stat->total_paid, 2) }}
                            </td>
                            <td class="py-2 px-3 text-red-600 font-semibold">
                                RWF{{ number_format($stat->total_remaining, 2) }}
                            </td>
                            <td class="py-2 px-3 font-semibold">
                                RWF {{ number_format($stat->total_amount, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Income Records</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(auth()->user()->hasAnyRole(['admin', 'accountant']))
        <div class="flex gap-2 mb-4">
            {{-- Download Buttons --}}
            <x-download-buttons 
                route="incomes.export" 
                filename="incomes" 
                size="sm" />
            
            <a href="{{ route('incomes.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Add New Income</a>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full theme-aware-bg-card border rounded">
            <thead>
                <tr class="theme-aware-bg-tertiary theme-aware-text-secondary">
                    <th class="py-2 px-4 border">ID</th>
                    <th class="py-2 px-4 border">Project</th>
                    <th class="py-2 px-4 border">Invoice #</th>
                    <th class="py-2 px-4 border">Amount Received</th>
                    <th class="py-2 px-4 border">Payment Status</th>
                    <th class="py-2 px-4 border">Amount Remaining</th>
                    <th class="py-2 px-4 border">Received At</th>
                    <th class="py-2 px-4 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incomes as $income)
                    <tr class="theme-aware-text-secondary text-center">
                        <td class="py-2 px-4 border">{{ $income->id }}</td>
                        <td class="py-2 px-4 border">{{ $income->project->name ?? 'N/A' }}</td>
                        <td class="py-2 px-4 border">{{ $income->invoice_number }}</td>
                        <td class="py-2 px-4 border">RWF {{ number_format($income->amount_received, 2) }}</td>
                        <td class="py-2 px-4 border">{{ $income->payment_status }}</td>
                        <td class="py-2 px-4 border">RWF {{ number_format($income->amount_remaining, 2) }}</td>
                        <td class="py-2 px-4 border">{{ $income->received_at->format('Y-m-d') }}</td>
                        <td class="py-2 px-4 border">
                            <a href="{{ route('incomes.show', $income->id) }}" class="text-blue-500 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-4 text-center">No income records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $incomes->links() }}
    </div>
</div>
@endsection

{{-- resources/views/admin/dashboard.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Admin Dashboard</h1>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Revenue --}}
        <div class="bg-white shadow rounded-2xl p-6">
            <h2 class="text-gray-500 text-sm">Total Revenue</h2>
            <p class="text-2xl font-bold text-green-600">
                ${{ number_format($revenue ?? 0, 2) }}
            </p>
            <span class="text-xs text-gray-400">Updated {{ now()->format('M d, Y') }}</span>
        </div>

        {{-- Expenses --}}
        <div class="bg-white shadow rounded-2xl p-6">
            <h2 class="text-gray-500 text-sm">Total Expenses</h2>
            <p class="text-2xl font-bold text-red-600">
                ${{ number_format($expenses ?? 0, 2) }}
            </p>
            <span class="text-xs text-gray-400">This Month</span>
        </div>

        {{-- Profit --}}
        <div class="bg-white shadow rounded-2xl p-6">
            <h2 class="text-gray-500 text-sm">Net Profit</h2>
            <p class="text-2xl font-bold text-blue-600">
                ${{ number_format(($revenue ?? 0) - ($expenses ?? 0), 2) }}
            </p>
            <span class="text-xs text-gray-400">Auto Calculated</span>
        </div>

        {{-- Projects --}}
        <div class="bg-white shadow rounded-2xl p-6">
            <h2 class="text-gray-500 text-sm">Projects</h2>
            <p class="text-2xl font-bold text-indigo-600">{{ $projects ?? 0 }}</p>
            <span class="text-xs text-gray-400">Active / Completed</span>
        </div>

        {{-- Clients --}}
        <div class="bg-white shadow rounded-2xl p-6">
            <h2 class="text-gray-500 text-sm">Clients</h2>
            <p class="text-2xl font-bold text-purple-600">{{ $clients ?? 0 }}</p>
            <span class="text-xs text-gray-400">Registered Clients</span>
        </div>

        {{-- Vendors --}}
        <div class="bg-white shadow rounded-2xl p-6">
            <h2 class="text-gray-500 text-sm">Vendors</h2>
            <p class="text-2xl font-bold text-yellow-600">{{ $vendors ?? 0 }}</p>
            <span class="text-xs text-gray-400">Active Vendors</span>
        </div>

        {{-- Employees --}}
        <div class="bg-white shadow rounded-2xl p-6">
            <h2 class="text-gray-500 text-sm">Employees</h2>
            <p class="text-2xl font-bold text-pink-600">{{ $employees ?? 0 }}</p>
            <span class="text-xs text-gray-400">On Payroll</span>
        </div>

        {{-- Users --}}
        <div class="bg-white shadow rounded-2xl p-6">
            <h2 class="text-gray-500 text-sm">System Users</h2>
            <p class="text-2xl font-bold text-gray-800">{{ $users ?? 0 }}</p>
            <span class="text-xs text-gray-400">Admins & Staff</span>
        </div>
    </div>

    {{-- Reports Section --}}
    <div class="mt-10 bg-white shadow rounded-2xl p-6">
        <h2 class="text-lg font-bold mb-4">Latest Reports</h2>
        <ul class="list-disc pl-6 text-gray-700">
            @forelse($reports as $report)
                <li>
                    {{ $report->title }} – 
                    <span class="text-sm text-gray-500">{{ $report->created_at->format('M d, Y') }}</span>
                </li>
            @empty
                <li class="text-gray-400">No reports available.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection

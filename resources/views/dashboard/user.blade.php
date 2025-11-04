{{-- resources/views/dashboard/user.blade.php --}}
@extends('layouts.app')
@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('title', 'User Dashboard - Project Overview & Personal Workspace | SiteLedger')
@section('meta_description', 'Personal construction finance dashboard with project overview and individual workspace. Access assigned projects, view relevant financial data, and track personal tasks.')
@section('meta_keywords', 'user dashboard, personal workspace, project overview, individual access, construction finance, personal dashboard')

@php
use Illuminate\Support\Facades\Auth;
@endphp

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold leading-tight">Dashboard</h1>
            <p class="text-sm theme-aware-text-muted mt-1">Overview of projects</p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
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
                    <div class="text-sm theme-aware-text-muted">Welcome</div>
                    <div class="text-2xl font-bold mt-1">{{ Auth::user()->name }}</div>
                    <div class="text-xs theme-aware-text-muted mt-1">Regular User</div>
                </div>
                <div class="flex-shrink-0 text-indigo-600 bg-indigo-50 rounded-full p-3">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/></svg>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border overflow-hidden transform hover:-translate-y-1 transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm theme-aware-text-muted">Status</div>
                    <div class="text-2xl font-bold mt-1 text-green-600">Active</div>
                    <div class="text-xs theme-aware-text-muted mt-1">Account active</div>
                </div>
                <div class="flex-shrink-0 text-green-600 bg-green-50 rounded-full p-3">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Projects --}}
    <div class="theme-aware-bg-card rounded-lg shadow-sm p-4 border">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold theme-aware-text-secondary">Recent Projects</h3>
            <a href="{{ route('projects.index') }}" class="text-indigo-600 text-sm hover:underline">View all projects</a>
        </div>

        @if($recentProjects->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="py-3 px-4 text-sm font-medium theme-aware-text-secondary">#</th>
                            <th class="py-3 px-4 text-sm font-medium theme-aware-text-secondary">Project Name</th>
                            <th class="py-3 px-4 text-sm font-medium theme-aware-text-secondary">Created Date</th>
                            <th class="py-3 px-4 text-sm font-medium theme-aware-text-secondary">Value</th>
                            <th class="py-3 px-4 text-sm font-medium theme-aware-text-secondary">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($recentProjects as $index => $proj)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-sm theme-aware-text-muted">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 text-sm font-medium theme-aware-text">{{ $proj->name ?? '—' }}</td>
                                <td class="py-3 px-4 text-sm theme-aware-text-secondary">{{ optional($proj->created_at)->format('Y-m-d') ?? '—' }}</td>
                                <td class="py-3 px-4 text-sm theme-aware-text-secondary">
                                    <span class="inline-block px-2 py-1 rounded bg-gray-100">
                                        {{ number_format($proj->contract_value ?? 0, 2) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <a href="{{ route('projects.show', $proj) }}" class="text-indigo-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <p class="theme-aware-text-muted">No projects available at this time.</p>
            </div>
        @endif
    </div>

    {{-- Info Box --}}
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <div>
                <h3 class="font-semibold text-blue-900">Limited Access</h3>
                <p class="text-sm text-blue-800 mt-1">
                    As a regular user, you have read-only access to project information. 
                    To manage projects, expenses, or payments, please contact an administrator.
                </p>
            </div>
        </div>
    </div>

    <div class="mt-6 text-sm theme-aware-text-muted">© {{ date('Y') }} {{ config('app.name', 'SiteLedger') }}</div>
</div>
@endsection

@push('styles')
<style>
    .shadow-sm { box-shadow: 0 6px 18px rgba(20,24,40,0.06); }
    .border { border: 1px solid rgba(17,24,39,0.04); }
</style>
@endpush

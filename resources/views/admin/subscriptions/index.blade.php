@extends('layouts.app')

@section('title', 'Tenant Subscriptions')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold theme-aware-text">Tenant Subscriptions</h1>
        <a href="{{ route('admin.subscriptions.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>Add Subscription
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="theme-aware-bg-card rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium theme-aware-text-muted">Total</p>
                    <p class="text-2xl font-semibold theme-aware-text">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium theme-aware-text-muted">Active</p>
                    <p class="text-2xl font-semibold theme-aware-text">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium theme-aware-text-muted">Expiring Soon</p>
                    <p class="text-2xl font-semibold theme-aware-text">{{ $stats['expiring_soon'] }}</p>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium theme-aware-text-muted">Past Due</p>
                    <p class="text-2xl font-semibold theme-aware-text">{{ $stats['past_due'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="theme-aware-bg-card rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b theme-aware-border">
            <h2 class="text-lg font-semibold theme-aware-text">All Subscriptions</h2>
        </div>
        
        @if($subscriptions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="theme-aware-bg-secondary">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">
                            Tenant
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">
                            Plan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">
                            Amount
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">
                            Billing Cycle
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">
                            Next Payment
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium theme-aware-text-muted uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="theme-aware-bg-card divide-y divide-gray-200">
                    @foreach($subscriptions as $subscription)
                    <tr class="hover:theme-aware-bg-secondary">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium theme-aware-text">
                                {{ $subscription->tenant->name }}
                            </div>
                            <div class="text-sm theme-aware-text-muted">
                                {{ $subscription->tenant->domain }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium theme-aware-text">
                                {{ $subscription->getPlanName() }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $subscription->getStatusColorClass() }}">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm theme-aware-text">
                            ${{ number_format($subscription->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm theme-aware-text">
                            {{ ucfirst($subscription->billing_cycle) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm theme-aware-text">
                            {{ $subscription->current_period_end ? $subscription->current_period_end->format('M j, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.subscriptions.show', $subscription) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.subscriptions.edit', $subscription) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t theme-aware-border">
            {{ $subscriptions->links() }}
        </div>
        @else
        <div class="px-6 py-8 text-center theme-aware-text-muted">
            <i class="fas fa-credit-card text-4xl mb-4"></i>
            <p class="text-lg">No subscriptions found</p>
            <p class="text-sm">Get started by creating your first subscription.</p>
        </div>
        @endif
    </div>
</div>
@endsection
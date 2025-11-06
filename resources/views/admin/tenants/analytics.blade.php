{{-- resources/views/admin/tenants/analytics.blade.php --}}
@extends('layouts.app')

@section('title', 'Tenant Analytics Dashboard | SiteLedger')
@section('meta_description', 'Advanced analytics and insights for all tenants. Monitor usage, performance, billing, and growth metrics across your multitenant platform.')
@section('meta_keywords', 'tenant analytics, multitenant metrics, business intelligence, platform analytics, usage statistics')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-xl shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center">
                    <div class="bg-white/20 rounded-lg p-2 mr-4">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    Tenant Analytics Dashboard
                </h1>
                <p class="text-purple-100 mt-2">Platform insights and performance metrics</p>
            </div>
            <div class="flex space-x-3">
                <select id="timeRange" class="bg-white/20 text-white border border-white/30 rounded-lg px-4 py-2">
                    <option value="7" class="text-gray-900">Last 7 days</option>
                    <option value="30" class="text-gray-900">Last 30 days</option>
                    <option value="90" class="text-gray-900">Last 90 days</option>
                    <option value="365" class="text-gray-900">Last year</option>
                </select>
                <button onclick="exportAnalytics()" class="bg-white text-purple-600 px-6 py-2 rounded-lg font-semibold hover:bg-purple-50 transition">
                    <i class="fas fa-download mr-2"></i>
                    Export Report
                </button>
            </div>
        </div>
    </div>

    {{-- Key Metrics Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Tenants --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Tenants</p>
                    <p class="text-3xl font-bold text-blue-600" id="totalTenants">{{ $analytics['total_tenants'] ?? 0 }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        +{{ $analytics['tenants_growth'] ?? 0 }}% this month
                    </p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-building text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Active Users --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Users</p>
                    <p class="text-3xl font-bold text-green-600" id="activeUsers">{{ $analytics['active_users'] ?? 0 }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        +{{ $analytics['users_growth'] ?? 0 }}% this month
                    </p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Revenue --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Monthly Revenue</p>
                    <p class="text-3xl font-bold text-purple-600" id="totalRevenue">${{ number_format($analytics['monthly_revenue'] ?? 0) }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        +{{ $analytics['revenue_growth'] ?? 0 }}% vs last month
                    </p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Platform Usage --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Avg Usage Score</p>
                    <p class="text-3xl font-bold text-orange-600" id="usageScore">{{ $analytics['usage_score'] ?? 0 }}%</p>
                    <p class="text-xs text-orange-600 mt-1">
                        Based on activity metrics
                    </p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <i class="fas fa-chart-pie text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Tenant Growth Chart --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Tenant Growth Over Time</h3>
                <div class="flex space-x-2">
                    <button class="text-xs px-3 py-1 bg-blue-100 text-blue-800 rounded-full">Monthly</button>
                    <button class="text-xs px-3 py-1 bg-gray-100 text-gray-600 rounded-full">Weekly</button>
                </div>
            </div>
            <div class="h-80">
                <canvas id="tenantGrowthChart"></canvas>
            </div>
        </div>

        {{-- Revenue Breakdown --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Revenue by Plan</h3>
                <select class="text-sm border border-gray-300 rounded px-3 py-1">
                    <option>This Month</option>
                    <option>Last Month</option>
                    <option>This Year</option>
                </select>
            </div>
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Usage Metrics --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        {{-- Feature Usage --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Popular Features</h3>
            <div class="space-y-4">
                @foreach($analytics['feature_usage'] ?? [] as $feature => $usage)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-{{ $feature['icon'] ?? 'cog' }} text-blue-600 text-sm"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $feature['name'] ?? $feature }}</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">{{ $usage['percentage'] ?? rand(60, 95) }}%</div>
                            <div class="text-xs text-gray-500">{{ $usage['users'] ?? rand(100, 500) }} users</div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $usage['percentage'] ?? rand(60, 95) }}%"></div>
                    </div>
                @endforeach
                
                {{-- Default data if no feature usage available --}}
                @if(empty($analytics['feature_usage']))
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-project-diagram text-blue-600 text-sm"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Project Management</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">87%</div>
                            <div class="text-xs text-gray-500">340 users</div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 87%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tasks text-green-600 text-sm"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Task Management</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">76%</div>
                            <div class="text-xs text-gray-500">298 users</div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 76%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-bar text-purple-600 text-sm"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">Reports & Analytics</span>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900">64%</div>
                            <div class="text-xs text-gray-500">251 users</div>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: 64%"></div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Subscription Plans --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Subscription Distribution</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                        <span class="text-sm font-medium text-gray-900">Enterprise</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold text-gray-900">{{ $analytics['plans']['enterprise'] ?? 15 }}</div>
                        <div class="text-xs text-gray-500">{{ number_format((($analytics['plans']['enterprise'] ?? 15) / ($analytics['total_tenants'] ?? 100)) * 100, 1) }}%</div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-600 rounded-full"></div>
                        <span class="text-sm font-medium text-gray-900">Professional</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold text-gray-900">{{ $analytics['plans']['professional'] ?? 45 }}</div>
                        <div class="text-xs text-gray-500">{{ number_format((($analytics['plans']['professional'] ?? 45) / ($analytics['total_tenants'] ?? 100)) * 100, 1) }}%</div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-gray-600 rounded-full"></div>
                        <span class="text-sm font-medium text-gray-900">Basic</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold text-gray-900">{{ $analytics['plans']['basic'] ?? 40 }}</div>
                        <div class="text-xs text-gray-500">{{ number_format((($analytics['plans']['basic'] ?? 40) / ($analytics['total_tenants'] ?? 100)) * 100, 1) }}%</div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <canvas id="subscriptionChart" width="300" height="300"></canvas>
            </div>
        </div>

        {{-- Activity Heatmap --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Activity Heatmap</h3>
            <div class="text-center text-sm text-gray-500 mb-4">Weekly Usage Patterns</div>
            
            {{-- Heatmap Grid --}}
            <div class="grid grid-cols-7 gap-1 mb-4">
                @for($day = 0; $day < 7; $day++)
                    <div class="text-xs text-center text-gray-500 p-1">
                        {{ ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][$day] }}
                    </div>
                @endfor
                
                @for($hour = 0; $hour < 24; $hour++)
                    @for($day = 0; $day < 7; $day++)
                        @php
                            $intensity = rand(0, 4);
                            $classes = [
                                'bg-gray-100',
                                'bg-green-200',
                                'bg-green-300',
                                'bg-green-400',
                                'bg-green-500'
                            ];
                        @endphp
                        <div class="w-3 h-3 rounded {{ $classes[$intensity] }}" 
                             title="{{ ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][$day] }} {{ $hour }}:00 - Activity: {{ $intensity * 25 }}%"></div>
                    @endfor
                @endfor
            </div>
            
            <div class="flex items-center justify-between text-xs text-gray-500">
                <span>Less</span>
                <div class="flex space-x-1">
                    <div class="w-3 h-3 bg-gray-100 rounded"></div>
                    <div class="w-3 h-3 bg-green-200 rounded"></div>
                    <div class="w-3 h-3 bg-green-300 rounded"></div>
                    <div class="w-3 h-3 bg-green-400 rounded"></div>
                    <div class="w-3 h-3 bg-green-500 rounded"></div>
                </div>
                <span>More</span>
            </div>
        </div>
    </div>

    {{-- Detailed Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Top Tenants by Usage --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Top Performing Tenants</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tenant</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Users</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Activity</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($analytics['top_tenants'] ?? [] as $tenant)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-white font-semibold text-xs">{{ strtoupper(substr($tenant['name'] ?? 'T', 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $tenant['name'] ?? 'Sample Tenant ' . ($loop->index + 1) }}</div>
                                            <div class="text-xs text-gray-500">{{ $tenant['plan'] ?? 'Professional' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $tenant['users'] ?? rand(10, 50) }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $tenant['activity'] ?? rand(60, 95) }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $tenant['activity'] ?? rand(60, 95) }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${{ number_format($tenant['revenue'] ?? rand(500, 2000)) }}</td>
                            </tr>
                        @endforeach
                        
                        {{-- Default data if no tenants available --}}
                        @if(empty($analytics['top_tenants']))
                            @for($i = 1; $i <= 5; $i++)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-white font-semibold text-xs">T{{ $i }}</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">Tenant {{ $i }}</div>
                                                <div class="text-xs text-gray-500">{{ ['Basic', 'Professional', 'Enterprise'][rand(0, 2)] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ rand(10, 50) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @php $activity = rand(60, 95); @endphp
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $activity }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $activity }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${{ number_format(rand(500, 2000)) }}</td>
                                </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Activity Log --}}
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Recent Platform Activity</h3>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @foreach($analytics['recent_activity'] ?? [] as $activity)
                    <div class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-{{ $activity['icon'] ?? 'user' }} text-blue-600 text-sm"></i>
                        </div>
                        <div class="flex-grow">
                            <div class="text-sm font-medium text-gray-900">{{ $activity['title'] ?? 'User Activity' }}</div>
                            <div class="text-xs text-gray-500">{{ $activity['description'] ?? 'Sample activity description' }}</div>
                            <div class="text-xs text-gray-400 mt-1">{{ $activity['time'] ?? now()->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach
                
                {{-- Default activity data --}}
                @if(empty($analytics['recent_activity']))
                    <div class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-plus text-green-600 text-sm"></i>
                        </div>
                        <div class="flex-grow">
                            <div class="text-sm font-medium text-gray-900">New tenant created</div>
                            <div class="text-xs text-gray-500">Construction Co. Ltd joined the platform</div>
                            <div class="text-xs text-gray-400 mt-1">2 hours ago</div>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-users text-blue-600 text-sm"></i>
                        </div>
                        <div class="flex-grow">
                            <div class="text-sm font-medium text-gray-900">Team invitation sent</div>
                            <div class="text-xs text-gray-500">5 new users invited to Tech Solutions</div>
                            <div class="text-xs text-gray-400 mt-1">4 hours ago</div>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-credit-card text-purple-600 text-sm"></i>
                        </div>
                        <div class="flex-grow">
                            <div class="text-sm font-medium text-gray-900">Subscription upgraded</div>
                            <div class="text-xs text-gray-500">Manufacturing Inc. upgraded to Enterprise</div>
                            <div class="text-xs text-gray-400 mt-1">6 hours ago</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    initializeTenantGrowthChart();
    initializeRevenueChart();
    initializeSubscriptionChart();
});

function initializeTenantGrowthChart() {
    const ctx = document.getElementById('tenantGrowthChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'New Tenants',
                data: [12, 19, 15, 25, 22, 30],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function initializeRevenueChart() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Basic', 'Professional', 'Enterprise'],
            datasets: [{
                data: [{{ $analytics['plans']['basic'] ?? 40 }}, {{ $analytics['plans']['professional'] ?? 45 }}, {{ $analytics['plans']['enterprise'] ?? 15 }}],
                backgroundColor: [
                    'rgb(156, 163, 175)',
                    'rgb(34, 197, 94)',
                    'rgb(59, 130, 246)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function initializeSubscriptionChart() {
    const ctx = document.getElementById('subscriptionChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Basic', 'Professional', 'Enterprise'],
            datasets: [{
                data: [{{ $analytics['plans']['basic'] ?? 40 }}, {{ $analytics['plans']['professional'] ?? 45 }}, {{ $analytics['plans']['enterprise'] ?? 15 }}],
                backgroundColor: [
                    'rgb(156, 163, 175)',
                    'rgb(34, 197, 94)',
                    'rgb(59, 130, 246)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function exportAnalytics() {
    const format = prompt('Export format (csv/pdf/excel):', 'csv');
    if (format) {
        window.open(`/admin/analytics/export?format=${format}&timeRange=${document.getElementById('timeRange').value}`, '_blank');
    }
}

// Time range change handler
document.getElementById('timeRange').addEventListener('change', function() {
    // Reload analytics data for selected time range
    window.location.href = `?timeRange=${this.value}`;
});
</script>
@endsection
{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'System Administration Dashboard - SiteLedger Management Console')
@section('meta_description', 'Comprehensive system administration dashboard for SiteLedger. Manage tenants, monitor system health, user activity, and maintain the multitenant accounting platform.')
@section('meta_keywords', 'system administration, tenant management, user monitoring, system health, admin dashboard, multitenant management')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="container mx-auto px-4 py-8">
        {{-- Page Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold flex items-center">
                        <div class="bg-white/20 rounded-lg p-2 mr-4">
                            <i class="fas fa-shield-alt text-2xl"></i>
                        </div>
                        System Administration
                    </h1>
                    <p class="text-blue-100 mt-2">Complete system overview and management console</p>
                </div>
                <div class="text-right">
                    <div class="text-sm opacity-90">Last updated</div>
                    <div class="text-lg font-semibold">{{ now()->format('M d, Y H:i') }}</div>
                </div>
            </div>
        </div>

        {{-- System Health Status Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- System Status --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">System Status</p>
                        <p class="text-2xl font-bold text-green-600">Healthy</p>
                        <p class="text-xs text-gray-500 mt-1">All services running</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-check-circle text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            {{-- Total Tenants --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Tenants</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $totalTenants ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">+{{ $newTenantsThisMonth ?? 0 }} this month</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-building text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            {{-- Total Users --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $totalUsers ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">+{{ $newUsersThisWeek ?? 0 }} this week</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <i class="fas fa-users text-2xl text-purple-600"></i>
                    </div>
                </div>
            </div>

            {{-- System Load --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">System Load</p>
                        <p class="text-2xl font-bold text-orange-600">{{ $systemLoad ?? 'Low' }}</p>
                        <p class="text-xs text-gray-500 mt-1">Server performance</p>
                    </div>
                    <div class="bg-orange-100 rounded-full p-3">
                        <i class="fas fa-tachometer-alt text-2xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions Panel --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-bolt text-blue-600 mr-2"></i>
                Quick Actions
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <a href="{{ route('admin.tenants.create') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                    <i class="fas fa-plus-circle text-2xl text-blue-600 mb-2"></i>
                    <span class="text-sm font-medium text-blue-800">New Tenant</span>
                </a>
                <a href="{{ route('users.create') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                    <i class="fas fa-user-plus text-2xl text-green-600 mb-2"></i>
                    <span class="text-sm font-medium text-green-800">Add User</span>
                </a>
                <button onclick="createBackup()" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                    <i class="fas fa-download text-2xl text-purple-600 mb-2"></i>
                    <span class="text-sm font-medium text-purple-800">Backup</span>
                </button>
                <a href="#system-logs" class="flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                    <i class="fas fa-list-alt text-2xl text-orange-600 mb-2"></i>
                    <span class="text-sm font-medium text-orange-800">View Logs</span>
                </a>
                <a href="#system-settings" class="flex flex-col items-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition">
                    <i class="fas fa-cog text-2xl text-red-600 mb-2"></i>
                    <span class="text-sm font-medium text-red-800">Settings</span>
                </a>
                <a href="#monitoring" class="flex flex-col items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                    <i class="fas fa-chart-line text-2xl text-indigo-600 mb-2"></i>
                    <span class="text-sm font-medium text-indigo-800">Monitor</span>
                </a>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Recent Activity --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-clock text-blue-600 mr-2"></i>
                        Recent System Activity
                    </h2>
                    <div class="space-y-4">
                        @forelse($recentActivities ?? [] as $activity)
                            <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-{{ $activity['color'] ?? 'blue' }}-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-{{ $activity['icon'] ?? 'info' }} text-{{ $activity['color'] ?? 'blue' }}-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="flex-grow">
                                    <p class="text-sm font-medium text-gray-900">{{ $activity['message'] ?? 'System activity' }}</p>
                                    <p class="text-xs text-gray-500">{{ $activity['timestamp'] ?? now()->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-info-circle text-4xl mb-2"></i>
                                <p>No recent activity to display</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- System Information --}}
            <div class="space-y-6">
                {{-- Server Information --}}
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-server text-green-600 mr-2"></i>
                        Server Info
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">PHP Version</span>
                            <span class="text-sm font-medium">{{ PHP_VERSION }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Laravel Version</span>
                            <span class="text-sm font-medium">{{ app()->version() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Environment</span>
                            <span class="text-sm font-medium capitalize">{{ config('app.env') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Database</span>
                            <span class="text-sm font-medium">{{ config('database.default') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Cache Driver</span>
                            <span class="text-sm font-medium">{{ config('cache.default') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Storage Information --}}
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-hdd text-purple-600 mr-2"></i>
                        Storage Usage
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600">Database Size</span>
                                <span class="text-sm font-medium">{{ $dbSize ?? '12.5 MB' }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 25%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600">Storage Files</span>
                                <span class="text-sm font-medium">{{ $storageSize ?? '8.3 MB' }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 15%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600">Cache Size</span>
                                <span class="text-sm font-medium">{{ $cacheSize ?? '2.1 MB' }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-orange-600 h-2 rounded-full" style="width: 5%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-pie text-indigo-600 mr-2"></i>
                        Today's Stats
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">New Registrations</span>
                            <span class="text-sm font-medium text-green-600">{{ $todayRegistrations ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Active Sessions</span>
                            <span class="text-sm font-medium text-blue-600">{{ $activeSessions ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">API Requests</span>
                            <span class="text-sm font-medium text-purple-600">{{ $apiRequests ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">System Errors</span>
                            <span class="text-sm font-medium text-red-600">{{ $systemErrors ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tenant Management Quick View --}}
        <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-building text-blue-600 mr-2"></i>
                    Tenant Overview
                </h2>
                <a href="{{ route('admin.tenants.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    Manage All Tenants
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentTenants ?? [] as $tenant)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-building text-blue-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $tenant['name'] ?? 'Tenant Name' }}</div>
                                            <div class="text-sm text-gray-500">{{ $tenant['domain'] ?? 'tenant.example.com' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $tenant['business_type'] ?? 'Construction' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $tenant['users_count'] ?? 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        {{ ($tenant['status'] ?? 'active') === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($tenant['status'] ?? 'active') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $tenant['created_at'] ?? now()->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <a href="#" class="text-green-600 hover:text-green-900">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No tenants found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function createBackup() {
    if (confirm('Create a system backup? This may take a few minutes.')) {
        // Show loading state
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
        button.disabled = true;
        
        // Simulate backup creation (replace with actual endpoint)
        fetch('/admin/backup', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Backup created successfully!');
        })
        .catch(error => {
            alert('Backup failed: ' + error.message);
        })
        .finally(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }
}

// Auto-refresh system stats every 30 seconds
setInterval(() => {
    fetch('/admin/stats', {
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update dynamic elements
        document.querySelector('[data-stat="active-sessions"]')?.textContent = data.activeSessions || '0';
        document.querySelector('[data-stat="api-requests"]')?.textContent = data.apiRequests || '0';
    })
    .catch(error => console.warn('Stats update failed:', error));
}, 30000);
</script>
@endsection
{{-- resources/views/admin/audit-logs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Audit Logs - System Administration | SiteLedger')
@section('meta_description', 'Comprehensive audit log monitoring and analysis for the SiteLedger multitenant accounting system. Track user activities, system events, and security incidents.')
@section('meta_keywords', 'audit logs, system monitoring, activity tracking, security logs, admin monitoring, compliance')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-red-600 to-pink-700 rounded-xl shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center">
                    <div class="bg-white/20 rounded-lg p-2 mr-4">
                        <i class="fas fa-shield-alt text-2xl"></i>
                    </div>
                    Audit Logs
                </h1>
                <p class="text-red-100 mt-2">Monitor system activities and security events</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="exportLogs()" class="bg-white/20 text-white px-6 py-3 rounded-lg font-semibold hover:bg-white/30 transition">
                    <i class="fas fa-download mr-2"></i>
                    Export Logs
                </button>
                <button onclick="clearOldLogs()" class="bg-white text-red-600 px-6 py-3 rounded-lg font-semibold hover:bg-red-50 transition">
                    <i class="fas fa-trash mr-2"></i>
                    Clear Old
                </button>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Logs (24h)</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-logs">{{ $stats['total_logs_24h'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 rounded-lg p-3">
                    <i class="fas fa-list text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs text-blue-600">
                    <i class="fas fa-clock mr-1"></i>
                    Last 24 hours
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Critical Events</p>
                    <p class="text-2xl font-bold text-gray-900" id="critical-logs">{{ $stats['critical_logs'] ?? 0 }}</p>
                </div>
                <div class="bg-red-100 rounded-lg p-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs text-red-600">
                    <i class="fas fa-arrow-up mr-1"></i>
                    Requires attention
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Login Attempts</p>
                    <p class="text-2xl font-bold text-gray-900" id="login-attempts">{{ $stats['login_attempts'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 rounded-lg p-3">
                    <i class="fas fa-sign-in-alt text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs text-green-600">
                    <i class="fas fa-check mr-1"></i>
                    Success rate: {{ $stats['login_success_rate'] ?? 0 }}%
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Failed Actions</p>
                    <p class="text-2xl font-bold text-gray-900" id="failed-actions">{{ $stats['failed_actions'] ?? 0 }}</p>
                </div>
                <div class="bg-yellow-100 rounded-lg p-3">
                    <i class="fas fa-times-circle text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs text-yellow-600">
                    <i class="fas fa-exclamation mr-1"></i>
                    Review required
                </span>
            </div>
        </div>
    </div>

    {{-- Filters and Search --}}
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Logs</label>
                <div class="relative">
                    <input type="text" 
                           id="search" 
                           placeholder="Search actions, users, IPs..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <div>
                <label for="severity_filter" class="block text-sm font-medium text-gray-700 mb-2">Severity</label>
                <select id="severity_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <option value="">All Severities</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="critical">Critical</option>
                </select>
            </div>

            <div>
                <label for="tenant_filter" class="block text-sm font-medium text-gray-700 mb-2">Tenant</label>
                <select id="tenant_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <option value="">All Tenants</option>
                    @foreach($tenants ?? [] as $tenant)
                        <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="action_filter" class="block text-sm font-medium text-gray-700 mb-2">Action Type</label>
                <select id="action_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <option value="">All Actions</option>
                    <option value="login">Login</option>
                    <option value="logout">Logout</option>
                    <option value="create">Create</option>
                    <option value="update">Update</option>
                    <option value="delete">Delete</option>
                    <option value="view">View</option>
                    <option value="export">Export</option>
                    <option value="import">Import</option>
                    <option value="system">System</option>
                </select>
            </div>

            <div>
                <label for="date_range" class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                <select id="date_range" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="last_7_days">Last 7 Days</option>
                    <option value="last_30_days">Last 30 Days</option>
                    <option value="last_90_days">Last 90 Days</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4">
            <div class="flex items-center space-x-4">
                <button onclick="clearFilters()" class="text-gray-500 hover:text-gray-700 text-sm">
                    <i class="fas fa-times mr-1"></i>
                    Clear Filters
                </button>
                <span class="text-sm text-gray-500" id="results-count">Showing all logs</span>
            </div>

            <div class="flex items-center space-x-2">
                <button onclick="refreshLogs()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                    <i class="fas fa-sync mr-1"></i>
                    Refresh
                </button>
                <button onclick="realTimeToggle()" id="realtime-btn" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 transition">
                    <i class="fas fa-play mr-1"></i>
                    Real-time
                </button>
            </div>
        </div>

        {{-- Custom Date Range --}}
        <div id="custom-date-range" class="hidden mt-4 grid grid-cols-2 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="datetime-local" 
                       id="start_date" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="datetime-local" 
                       id="end_date" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
            </div>
        </div>
    </div>

    {{-- Audit Logs Table --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800">Audit Logs</h2>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500">
                        Auto-refresh: <span id="auto-refresh-status" class="font-semibold">OFF</span>
                    </div>
                    <div class="text-sm text-gray-500">
                        Last updated: <span id="last-updated">{{ now()->format('Y-m-d H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full" id="logs-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('timestamp')">
                            Timestamp <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('severity')">
                            Severity <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('action')">
                            Action <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('user')">
                            User <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('tenant')">
                            Tenant <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Details
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            IP Address
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="logs-tbody">
                    @forelse($logs ?? [] as $log)
                        <tr class="hover:bg-gray-50 log-row" data-log-id="{{ $log->id }}">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                                <div class="text-xs text-gray-500">
                                    {{ $log->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $log->severity === 'critical' ? 'bg-red-100 text-red-800' : 
                                       ($log->severity === 'high' ? 'bg-orange-100 text-orange-800' : 
                                        ($log->severity === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                    <i class="fas fa-{{ $log->severity === 'critical' ? 'exclamation-triangle' : 
                                                          ($log->severity === 'high' ? 'exclamation' : 
                                                           ($log->severity === 'medium' ? 'info-circle' : 'check')) }} mr-1"></i>
                                    {{ ucfirst($log->severity) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $log->action }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($log->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($log->user)
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-semibold mr-2">
                                            {{ substr($log->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $log->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $log->user->role ?? 'user' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">System</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($log->tenant)
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">
                                        {{ $log->tenant->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">Global</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ Str::limit($log->description, 100) }}
                                @if($log->metadata)
                                    <button onclick="showMetadata({{ $log->id }})" class="text-blue-600 hover:text-blue-800 text-xs ml-2">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $log->ip_address ?? 'Unknown' }}
                                @if($log->user_agent)
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ Str::limit($log->user_agent, 30) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <button onclick="viewLogDetails({{ $log->id }})" 
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        View
                                    </button>
                                    @if($log->severity === 'critical' || $log->severity === 'high')
                                        <button onclick="investigateLog({{ $log->id }})" 
                                                class="text-red-600 hover:text-red-900 text-sm font-medium">
                                            Investigate
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-clipboard-list text-4xl mb-4"></i>
                                <p class="text-lg">No audit logs found</p>
                                <p class="text-sm">Logs will appear here as users perform actions</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(isset($logs) && $logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    {{-- Log Details Modal --}}
    <div id="log-details-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Audit Log Details</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-6" id="log-details-content">
                    {{-- Content will be loaded dynamically --}}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentSort = { column: 'timestamp', direction: 'desc' };
let realTimeMode = false;
let realTimeInterval;

// Real-time search functionality
document.getElementById('search').addEventListener('input', function() {
    filterLogs();
});

// Filter change handlers
['severity_filter', 'tenant_filter', 'action_filter', 'date_range'].forEach(filterId => {
    document.getElementById(filterId).addEventListener('change', filterLogs);
});

// Custom date range handler
document.getElementById('date_range').addEventListener('change', function() {
    const customDateRange = document.getElementById('custom-date-range');
    if (this.value === 'custom') {
        customDateRange.classList.remove('hidden');
    } else {
        customDateRange.classList.add('hidden');
    }
    filterLogs();
});

function filterLogs() {
    const search = document.getElementById('search').value.toLowerCase();
    const severityFilter = document.getElementById('severity_filter').value;
    const tenantFilter = document.getElementById('tenant_filter').value;
    const actionFilter = document.getElementById('action_filter').value;
    const dateRange = document.getElementById('date_range').value;
    
    const rows = document.querySelectorAll('.log-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const action = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const user = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
        const tenant = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
        const severity = row.querySelector('.inline-flex').textContent.toLowerCase();
        const details = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
        
        const matchesSearch = search === '' || action.includes(search) || user.includes(search) || details.includes(search);
        const matchesSeverity = severityFilter === '' || severity.includes(severityFilter);
        const matchesTenant = tenantFilter === '' || tenant.includes(tenantFilter.toLowerCase());
        const matchesAction = actionFilter === '' || action.includes(actionFilter);
        
        const shouldShow = matchesSearch && matchesSeverity && matchesTenant && matchesAction;
        row.style.display = shouldShow ? '' : 'none';
        
        if (shouldShow) visibleCount++;
    });
    
    document.getElementById('results-count').textContent = `Showing ${visibleCount} logs`;
}

function clearFilters() {
    document.getElementById('search').value = '';
    document.getElementById('severity_filter').value = '';
    document.getElementById('tenant_filter').value = '';
    document.getElementById('action_filter').value = '';
    document.getElementById('date_range').value = 'today';
    document.getElementById('custom-date-range').classList.add('hidden');
    filterLogs();
}

function sortTable(column) {
    if (currentSort.column === column) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.column = column;
        currentSort.direction = 'desc';
    }
    
    // Implement sorting logic here
    console.log(`Sorting by ${column} ${currentSort.direction}`);
}

function refreshLogs() {
    const button = event.target;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Refreshing...';
    button.disabled = true;
    
    // Refresh logs from server
    fetch('/admin/audit-logs/refresh', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Update table content
        document.getElementById('logs-tbody').innerHTML = html;
        document.getElementById('last-updated').textContent = new Date().toLocaleString();
        filterLogs(); // Reapply filters
    })
    .catch(error => {
        console.error('Error refreshing logs:', error);
    })
    .finally(() => {
        button.innerHTML = '<i class="fas fa-sync mr-1"></i>Refresh';
        button.disabled = false;
    });
}

function realTimeToggle() {
    const button = document.getElementById('realtime-btn');
    const status = document.getElementById('auto-refresh-status');
    
    if (realTimeMode) {
        // Stop real-time mode
        clearInterval(realTimeInterval);
        realTimeMode = false;
        button.innerHTML = '<i class="fas fa-play mr-1"></i>Real-time';
        button.classList.remove('bg-red-600', 'hover:bg-red-700');
        button.classList.add('bg-green-600', 'hover:bg-green-700');
        status.textContent = 'OFF';
    } else {
        // Start real-time mode
        realTimeMode = true;
        button.innerHTML = '<i class="fas fa-stop mr-1"></i>Stop';
        button.classList.remove('bg-green-600', 'hover:bg-green-700');
        button.classList.add('bg-red-600', 'hover:bg-red-700');
        status.textContent = 'ON';
        
        // Refresh every 5 seconds
        realTimeInterval = setInterval(() => {
            refreshLogs();
        }, 5000);
    }
}

function viewLogDetails(logId) {
    fetch(`/admin/audit-logs/${logId}`)
        .then(response => response.json())
        .then(data => {
            const content = document.getElementById('log-details-content');
            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Basic Information</h4>
                        <div class="space-y-2 text-sm">
                            <div><strong>ID:</strong> ${data.id}</div>
                            <div><strong>Timestamp:</strong> ${data.created_at}</div>
                            <div><strong>Action:</strong> ${data.action}</div>
                            <div><strong>Severity:</strong> <span class="px-2 py-1 rounded text-xs font-medium bg-${data.severity === 'critical' ? 'red' : data.severity === 'high' ? 'orange' : data.severity === 'medium' ? 'yellow' : 'green'}-100 text-${data.severity === 'critical' ? 'red' : data.severity === 'high' ? 'orange' : data.severity === 'medium' ? 'yellow' : 'green'}-800">${data.severity}</span></div>
                            <div><strong>User:</strong> ${data.user ? data.user.name : 'System'}</div>
                            <div><strong>Tenant:</strong> ${data.tenant ? data.tenant.name : 'Global'}</div>
                            <div><strong>IP Address:</strong> ${data.ip_address || 'Unknown'}</div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">Description</h4>
                        <p class="text-sm text-gray-600 mb-4">${data.description}</p>
                        
                        ${data.user_agent ? `
                        <h4 class="font-semibold text-gray-800 mb-2">User Agent</h4>
                        <p class="text-xs text-gray-500 mb-4">${data.user_agent}</p>
                        ` : ''}
                    </div>
                </div>
                
                ${data.metadata ? `
                <div class="mt-6">
                    <h4 class="font-semibold text-gray-800 mb-2">Metadata</h4>
                    <pre class="bg-gray-100 p-4 rounded-lg text-xs overflow-x-auto">${JSON.stringify(data.metadata, null, 2)}</pre>
                </div>
                ` : ''}
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button onclick="closeModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                        Close
                    </button>
                    ${data.severity === 'critical' || data.severity === 'high' ? `
                    <button onclick="investigateLog(${data.id})" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                        Investigate
                    </button>
                    ` : ''}
                </div>
            `;
            document.getElementById('log-details-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error loading log details:', error);
            alert('Failed to load log details.');
        });
}

function showMetadata(logId) {
    // Similar to viewLogDetails but focus on metadata
    viewLogDetails(logId);
}

function investigateLog(logId) {
    if (confirm('Mark this log for investigation? This will create an investigation task.')) {
        fetch(`/admin/audit-logs/${logId}/investigate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Investigation task created successfully.');
                closeModal();
            } else {
                alert('Failed to create investigation task.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }
}

function closeModal() {
    document.getElementById('log-details-modal').classList.add('hidden');
}

function exportLogs() {
    const filters = {
        search: document.getElementById('search').value,
        severity: document.getElementById('severity_filter').value,
        tenant: document.getElementById('tenant_filter').value,
        action: document.getElementById('action_filter').value,
        date_range: document.getElementById('date_range').value
    };
    
    if (filters.date_range === 'custom') {
        filters.start_date = document.getElementById('start_date').value;
        filters.end_date = document.getElementById('end_date').value;
    }
    
    const queryString = new URLSearchParams(filters).toString();
    window.open(`/admin/audit-logs/export?${queryString}`, '_blank');
}

function clearOldLogs() {
    if (confirm('Clear logs older than 90 days? This action cannot be undone.')) {
        fetch('/admin/audit-logs/clear-old', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Cleared ${data.deleted_count} old log entries.`);
                refreshLogs();
            } else {
                alert('Failed to clear old logs.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while clearing logs.');
        });
    }
}

// Auto-refresh stats every 30 seconds
setInterval(() => {
    fetch('/admin/audit-logs/stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-logs').textContent = data.total_logs_24h;
            document.getElementById('critical-logs').textContent = data.critical_logs;
            document.getElementById('login-attempts').textContent = data.login_attempts;
            document.getElementById('failed-actions').textContent = data.failed_actions;
        })
        .catch(error => {
            console.warn('Failed to refresh stats:', error);
        });
}, 30000);

// Close modal when clicking outside
document.getElementById('log-details-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection
{{-- resources/views/admin/tenants/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Tenant Details: ' . $tenant->name . ' - System Administration | SiteLedger')
@section('meta_description', 'View comprehensive tenant details for ' . $tenant->name . ' including business information, statistics, users, and activity logs.')
@section('meta_keywords', 'tenant details, business overview, tenant management, user statistics, activity monitoring')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-xl shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center mb-2">
                    <div class="bg-white/20 rounded-lg p-2 mr-4">
                        <i class="fas fa-building text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">{{ $tenant->name }}</h1>
                        <p class="text-purple-100">{{ $tenant->domain }}.{{ config('app.domain', 'siteledger.com') }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 text-sm">
                    <span class="bg-{{ $tenant->status === 'active' ? 'green' : ($tenant->status === 'suspended' ? 'red' : 'gray') }}-500/20 px-3 py-1 rounded-full">
                        {{ ucfirst($tenant->status) }}
                    </span>
                    <span class="bg-white/20 px-3 py-1 rounded-full">{{ ucfirst($tenant->subscription_plan) }} Plan</span>
                    <span class="bg-white/20 px-3 py-1 rounded-full">{{ $tenant->users_count ?? 0 }} Users</span>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.tenants.edit', $tenant) }}" class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-purple-50 transition">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('admin.tenants.index') }}" class="bg-purple-500/30 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-500/50 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Statistics Overview --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $tenant->users_count ?? 0 }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-lg p-3">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-xs text-gray-500">
                            of {{ $tenant->max_users ?? 'unlimited' }} maximum
                        </span>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Projects</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $tenant->projects_count ?? 0 }}</p>
                        </div>
                        <div class="bg-green-100 rounded-lg p-3">
                            <i class="fas fa-project-diagram text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-xs text-green-600">
                            <i class="fas fa-arrow-up mr-1"></i>
                            Active this month
                        </span>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $tenant->total_revenue ?? 0 }} {{ $tenant->currency ?? 'RWF' }}</p>
                        </div>
                        <div class="bg-purple-100 rounded-lg p-3">
                            <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-xs text-purple-600">
                            <i class="fas fa-arrow-up mr-1"></i>
                            This month
                        </span>
                    </div>
                </div>
            </div>

            {{-- Business Information --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                    Business Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Business Name</label>
                        <p class="text-gray-900 font-semibold">{{ $tenant->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Domain</label>
                        <p class="text-gray-900 font-semibold">{{ $tenant->domain }}.{{ config('app.domain') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Business Type</label>
                        <p class="text-gray-900 font-semibold">{{ ucfirst($tenant->business_type ?? 'Not specified') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Contact Email</label>
                        <p class="text-gray-900 font-semibold">
                            <a href="mailto:{{ $tenant->contact_email }}" class="text-blue-600 hover:text-blue-800">
                                {{ $tenant->contact_email }}
                            </a>
                        </p>
                    </div>

                    @if($tenant->contact_phone)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Contact Phone</label>
                        <p class="text-gray-900 font-semibold">
                            <a href="tel:{{ $tenant->contact_phone }}" class="text-blue-600 hover:text-blue-800">
                                {{ $tenant->contact_phone }}
                            </a>
                        </p>
                    </div>
                    @endif

                    @if($tenant->registration_number)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Registration Number</label>
                        <p class="text-gray-900 font-semibold">{{ $tenant->registration_number }}</p>
                    </div>
                    @endif

                    @if($tenant->address)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                        <p class="text-gray-900">{{ $tenant->address }}</p>
                    </div>
                    @endif

                    @if($tenant->description)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                        <p class="text-gray-900">{{ $tenant->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Subscription & Settings --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-cog text-green-600 mr-3"></i>
                    Subscription & Settings
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Subscription Plan</label>
                        <div class="flex items-center">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ ucfirst($tenant->subscription_plan) }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <div class="flex items-center">
                            <span class="bg-{{ $tenant->status === 'active' ? 'green' : ($tenant->status === 'suspended' ? 'red' : 'gray') }}-100 text-{{ $tenant->status === 'active' ? 'green' : ($tenant->status === 'suspended' ? 'red' : 'gray') }}-800 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Timezone</label>
                        <p class="text-gray-900 font-semibold">{{ $tenant->timezone ?? 'UTC' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Default Currency</label>
                        <p class="text-gray-900 font-semibold">{{ $tenant->currency ?? 'RWF' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Created Date</label>
                        <p class="text-gray-900 font-semibold">{{ $tenant->created_at->format('M j, Y g:i A') }}</p>
                    </div>

                    @if($tenant->trial_ends_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Trial Ends</label>
                        <p class="text-gray-900 font-semibold {{ $tenant->trial_ends_at->isPast() ? 'text-red-600' : 'text-orange-600' }}">
                            {{ $tenant->trial_ends_at->format('M j, Y') }}
                            @if($tenant->trial_ends_at->isPast())
                                (Expired)
                            @else
                                ({{ $tenant->trial_ends_at->diffForHumans() }})
                            @endif
                        </p>
                    </div>
                    @endif
                </div>

                @if(isset($tenant->features) && count($tenant->features) > 0)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-500 mb-3">Enabled Features</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tenant->features as $feature)
                            <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm">
                                {{ str_replace('_', ' ', ucfirst($feature)) }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Users List --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-users text-purple-600 mr-3"></i>
                        Users ({{ $tenant->users_count ?? 0 }})
                    </h2>
                    <a href="{{ route('admin.tenants.users', $tenant) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                        View All Users <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                @if(isset($tenant->users) && $tenant->users->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">User</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Role</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Last Login</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tenant->users->take(5) as $user)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-semibold mr-3">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm">
                                                {{ ucfirst($user->role ?? 'user') }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="bg-{{ $user->email_verified_at ? 'green' : 'yellow' }}-100 text-{{ $user->email_verified_at ? 'green' : 'yellow' }}-800 px-2 py-1 rounded text-sm">
                                                {{ $user->email_verified_at ? 'Active' : 'Pending' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-gray-500">
                                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-users text-3xl mb-3"></i>
                        <p>No users found for this tenant</p>
                    </div>
                @endif
            </div>

            {{-- Recent Activity --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-clock text-orange-600 mr-3"></i>
                        Recent Activity
                    </h2>
                    <a href="{{ route('admin.audit-logs', ['tenant_id' => $tenant->id]) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                        View All Activity <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                @if(isset($tenant->recent_activity) && $tenant->recent_activity->count() > 0)
                    <div class="space-y-4">
                        @foreach($tenant->recent_activity->take(10) as $activity)
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-8 h-8 bg-{{ $activity->severity === 'high' ? 'red' : ($activity->severity === 'medium' ? 'yellow' : 'blue') }}-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-{{ $activity->severity === 'high' ? 'exclamation-triangle' : ($activity->severity === 'medium' ? 'info-circle' : 'check') }} text-{{ $activity->severity === 'high' ? 'red' : ($activity->severity === 'medium' ? 'yellow' : 'blue') }}-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-900 font-medium">{{ $activity->action }}</p>
                                    <p class="text-sm text-gray-600">{{ $activity->description }}</p>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-xs text-gray-500">
                                            by {{ $activity->user ? $activity->user->name : 'System' }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-clock text-3xl mb-3"></i>
                        <p>No recent activity found</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('admin.tenants.edit', $tenant) }}" 
                       class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700 transition text-center block">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Tenant
                    </a>
                    
                    <a href="{{ route('admin.tenants.users', $tenant) }}" 
                       class="w-full bg-green-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-green-700 transition text-center block">
                        <i class="fas fa-users mr-2"></i>
                        Manage Users
                    </a>
                    
                    <a href="{{ route('admin.tenants.invitations.index', $tenant) }}" 
                       class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-indigo-700 transition text-center block">
                        <i class="fas fa-user-plus mr-2"></i>
                        Manage Invitations
                    </a>
                    
                    <button onclick="generateReport()" 
                            class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-purple-700 transition">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Generate Report
                    </button>
                    
                    @if($tenant->status === 'active')
                        <button onclick="suspendTenant()" 
                                class="w-full bg-yellow-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-yellow-700 transition">
                            <i class="fas fa-pause mr-2"></i>
                            Suspend Tenant
                        </button>
                    @else
                        <button onclick="activateTenant()" 
                                class="w-full bg-green-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-green-700 transition">
                            <i class="fas fa-play mr-2"></i>
                            Activate Tenant
                        </button>
                    @endif
                </div>
            </div>

            {{-- System Health --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">System Health</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Database Status:</span>
                        <span class="text-green-600 font-semibold">
                            <i class="fas fa-check-circle mr-1"></i>
                            Healthy
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Last Backup:</span>
                        <span class="text-blue-600 font-semibold text-sm">
                            {{ $tenant->last_backup_at ? $tenant->last_backup_at->diffForHumans() : 'Never' }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Storage Used:</span>
                        <span class="font-semibold text-sm">
                            {{ $tenant->storage_used ?? '0' }} MB
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">API Calls (Today):</span>
                        <span class="font-semibold text-sm">
                            {{ $tenant->api_calls_today ?? 0 }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Logo Display --}}
            @if($tenant->logo)
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Business Logo</h3>
                <div class="text-center">
                    <img src="{{ Storage::url($tenant->logo) }}" 
                         alt="{{ $tenant->name }} Logo" 
                         class="max-h-32 w-auto mx-auto object-contain">
                </div>
            </div>
            @endif

            {{-- Contact Information --}}
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Contact</h3>
                
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-blue-600 w-5 mr-3"></i>
                        <a href="mailto:{{ $tenant->contact_email }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            {{ $tenant->contact_email }}
                        </a>
                    </div>
                    
                    @if($tenant->contact_phone)
                    <div class="flex items-center">
                        <i class="fas fa-phone text-green-600 w-5 mr-3"></i>
                        <a href="tel:{{ $tenant->contact_phone }}" class="text-green-600 hover:text-green-800 text-sm">
                            {{ $tenant->contact_phone }}
                        </a>
                    </div>
                    @endif
                    
                    <div class="flex items-center">
                        <i class="fas fa-globe text-purple-600 w-5 mr-3"></i>
                        <a href="https://{{ $tenant->domain }}.{{ config('app.domain') }}" 
                           target="_blank" 
                           class="text-purple-600 hover:text-purple-800 text-sm">
                            Visit Site <i class="fas fa-external-link-alt ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateReport() {
    // Show loading state
    event.target.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';
    event.target.disabled = true;
    
    // Generate tenant report
    fetch(`/admin/tenants/{{ $tenant->id }}/report`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.blob())
    .then(blob => {
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = `{{ $tenant->name }}_report_${new Date().toISOString().slice(0, 10)}.pdf`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        
        // Reset button
        event.target.innerHTML = '<i class="fas fa-chart-bar mr-2"></i>Generate Report';
        event.target.disabled = false;
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to generate report. Please try again.');
        
        // Reset button
        event.target.innerHTML = '<i class="fas fa-chart-bar mr-2"></i>Generate Report';
        event.target.disabled = false;
    });
}

function suspendTenant() {
    if (confirm('Are you sure you want to suspend this tenant? Users will not be able to access their data.')) {
        updateTenantStatus('suspended');
    }
}

function activateTenant() {
    if (confirm('Are you sure you want to activate this tenant?')) {
        updateTenantStatus('active');
    }
}

function updateTenantStatus(status) {
    fetch(`/admin/tenants/{{ $tenant->id }}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to update tenant status. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Auto-refresh stats every 30 seconds
setInterval(() => {
    fetch(`/admin/tenants/{{ $tenant->id }}/stats`)
        .then(response => response.json())
        .then(data => {
            if (data.users_count !== undefined) {
                document.querySelector('.users-count').textContent = data.users_count;
            }
            if (data.projects_count !== undefined) {
                document.querySelector('.projects-count').textContent = data.projects_count;
            }
            if (data.total_revenue !== undefined) {
                document.querySelector('.revenue-count').textContent = data.total_revenue + ' ' + data.currency;
            }
        })
        .catch(error => {
            console.warn('Failed to refresh stats:', error);
        });
}, 30000);
</script>
@endsection
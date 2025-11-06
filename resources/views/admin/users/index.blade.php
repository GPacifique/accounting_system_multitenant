{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'User Management - System Administration | SiteLedger')
@section('meta_description', 'Comprehensive user management interface for administrators. View, create, edit, and manage user accounts across all tenants.')
@section('meta_keywords', 'user management, admin interface, user accounts, role management, system administration')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-xl shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center">
                    <div class="bg-white/20 rounded-lg p-2 mr-4">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    User Management
                </h1>
                <p class="text-indigo-100 mt-2">Manage user accounts across all tenants</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-indigo-50 transition">
                <i class="fas fa-plus mr-2"></i>
                Create User
            </a>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-users">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 rounded-lg p-3">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs text-blue-600">
                    <i class="fas fa-arrow-up mr-1"></i>
                    {{ $stats['users_growth'] ?? 0 }}% from last month
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Users</p>
                    <p class="text-2xl font-bold text-gray-900" id="active-users">{{ $stats['active_users'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 rounded-lg p-3">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs text-green-600">
                    <i class="fas fa-check mr-1"></i>
                    {{ number_format(($stats['active_users'] ?? 0) / max($stats['total_users'] ?? 1, 1) * 100, 1) }}% activation rate
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Users</p>
                    <p class="text-2xl font-bold text-gray-900" id="pending-users">{{ $stats['pending_users'] ?? 0 }}</p>
                </div>
                <div class="bg-yellow-100 rounded-lg p-3">
                    <i class="fas fa-user-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs text-yellow-600">
                    <i class="fas fa-clock mr-1"></i>
                    Awaiting verification
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Admins</p>
                    <p class="text-2xl font-bold text-gray-900" id="admin-users">{{ $stats['admin_users'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 rounded-lg p-3">
                    <i class="fas fa-user-shield text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs text-purple-600">
                    <i class="fas fa-shield-alt mr-1"></i>
                    System administrators
                </span>
            </div>
        </div>
    </div>

    {{-- Filters and Search --}}
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Users</label>
                <div class="relative">
                    <input type="text" 
                           id="search" 
                           placeholder="Search by name, email, or tenant..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <div>
                <label for="tenant_filter" class="block text-sm font-medium text-gray-700 mb-2">Filter by Tenant</label>
                <select id="tenant_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">All Tenants</option>
                    @foreach($tenants ?? [] as $tenant)
                        <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="role_filter" class="block text-sm font-medium text-gray-700 mb-2">Filter by Role</label>
                <select id="role_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="accountant">Accountant</option>
                    <option value="user">User</option>
                </select>
            </div>

            <div>
                <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                <select id="status_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending Verification</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4">
            <div class="flex items-center space-x-4">
                <button onclick="clearFilters()" class="text-gray-500 hover:text-gray-700 text-sm">
                    <i class="fas fa-times mr-1"></i>
                    Clear Filters
                </button>
                <span class="text-sm text-gray-500" id="results-count">Showing all users</span>
            </div>

            <div class="flex items-center space-x-2">
                <button onclick="exportUsers()" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 transition">
                    <i class="fas fa-download mr-1"></i>
                    Export
                </button>
                <button onclick="bulkActions()" class="bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-700 transition">
                    <i class="fas fa-tasks mr-1"></i>
                    Bulk Actions
                </button>
            </div>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800">Users List</h2>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center text-sm">
                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                        Select All
                    </label>
                    <div class="text-sm text-gray-500">
                        <span id="selected-count">0</span> selected
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full" id="users-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-12 px-6 py-3">
                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('name')">
                            User <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('tenant')">
                            Tenant <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('role')">
                            Role <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('status')">
                            Status <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('last_login')">
                            Last Login <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="users-tbody">
                    @forelse($users ?? [] as $user)
                        <tr class="hover:bg-gray-50 user-row" data-user-id="{{ $user->id }}">
                            <td class="px-6 py-4">
                                <input type="checkbox" 
                                       class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                                       value="{{ $user->id }}">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->tenant)
                                    <div class="flex items-center">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-medium">
                                            {{ $user->tenant->name }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">No tenant</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-{{ $user->role === 'admin' ? 'red' : ($user->role === 'manager' ? 'purple' : ($user->role === 'accountant' ? 'green' : 'gray')) }}-100 text-{{ $user->role === 'admin' ? 'red' : ($user->role === 'manager' ? 'purple' : ($user->role === 'accountant' ? 'green' : 'gray')) }}-800 px-2 py-1 rounded text-sm font-medium">
                                    {{ ucfirst($user->role ?? 'user') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->email_verified_at)
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-medium">
                                        Active
                                    </span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm font-medium">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.users.show', $user) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        View
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                       class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        Edit
                                    </a>
                                    @if(!$user->email_verified_at)
                                        <button onclick="resendVerification({{ $user->id }})" 
                                                class="text-green-600 hover:text-green-900 text-sm font-medium">
                                            Verify
                                        </button>
                                    @endif
                                    <button onclick="suspendUser({{ $user->id }})" 
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Suspend
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-users text-4xl mb-4"></i>
                                <p class="text-lg">No users found</p>
                                <p class="text-sm">Start by creating your first user account</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(isset($users) && $users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Bulk Actions Modal --}}
    <div id="bulk-actions-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Bulk Actions</h3>
                
                <div class="space-y-3">
                    <button onclick="bulkVerify()" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-check mr-2"></i>
                        Verify Selected Users
                    </button>
                    
                    <button onclick="bulkSuspend()" class="w-full bg-yellow-600 text-white py-2 px-4 rounded-lg hover:bg-yellow-700 transition">
                        <i class="fas fa-pause mr-2"></i>
                        Suspend Selected Users
                    </button>
                    
                    <button onclick="bulkDelete()" class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Selected Users
                    </button>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeBulkModal()" class="bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentSort = { column: 'name', direction: 'asc' };
let selectedUsers = new Set();

// Real-time search functionality
document.getElementById('search').addEventListener('input', function() {
    filterUsers();
});

// Filter change handlers
['tenant_filter', 'role_filter', 'status_filter'].forEach(filterId => {
    document.getElementById(filterId).addEventListener('change', filterUsers);
});

// Select all functionality
document.getElementById('select-all').addEventListener('change', function() {
    const isChecked = this.checked;
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = isChecked;
        if (isChecked) {
            selectedUsers.add(checkbox.value);
        } else {
            selectedUsers.delete(checkbox.value);
        }
    });
    updateSelectedCount();
});

// Individual checkbox handlers
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('user-checkbox')) {
        if (e.target.checked) {
            selectedUsers.add(e.target.value);
        } else {
            selectedUsers.delete(e.target.value);
        }
        updateSelectedCount();
    }
});

function updateSelectedCount() {
    document.getElementById('selected-count').textContent = selectedUsers.size;
}

function filterUsers() {
    const search = document.getElementById('search').value.toLowerCase();
    const tenantFilter = document.getElementById('tenant_filter').value;
    const roleFilter = document.getElementById('role_filter').value;
    const statusFilter = document.getElementById('status_filter').value;
    
    const rows = document.querySelectorAll('.user-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const tenant = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const role = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
        const status = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
        
        const matchesSearch = search === '' || name.includes(search) || tenant.includes(search);
        const matchesTenant = tenantFilter === '' || tenant.includes(tenantFilter.toLowerCase());
        const matchesRole = roleFilter === '' || role.includes(roleFilter);
        const matchesStatus = statusFilter === '' || status.includes(statusFilter);
        
        const shouldShow = matchesSearch && matchesTenant && matchesRole && matchesStatus;
        row.style.display = shouldShow ? '' : 'none';
        
        if (shouldShow) visibleCount++;
    });
    
    document.getElementById('results-count').textContent = `Showing ${visibleCount} users`;
}

function clearFilters() {
    document.getElementById('search').value = '';
    document.getElementById('tenant_filter').value = '';
    document.getElementById('role_filter').value = '';
    document.getElementById('status_filter').value = '';
    filterUsers();
}

function sortTable(column) {
    if (currentSort.column === column) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.column = column;
        currentSort.direction = 'asc';
    }
    
    // Implement sorting logic here
    console.log(`Sorting by ${column} ${currentSort.direction}`);
}

function exportUsers() {
    const filters = {
        search: document.getElementById('search').value,
        tenant: document.getElementById('tenant_filter').value,
        role: document.getElementById('role_filter').value,
        status: document.getElementById('status_filter').value
    };
    
    const queryString = new URLSearchParams(filters).toString();
    window.open(`/admin/users/export?${queryString}`, '_blank');
}

function bulkActions() {
    if (selectedUsers.size === 0) {
        alert('Please select at least one user.');
        return;
    }
    document.getElementById('bulk-actions-modal').classList.remove('hidden');
}

function closeBulkModal() {
    document.getElementById('bulk-actions-modal').classList.add('hidden');
}

function bulkVerify() {
    if (confirm(`Verify ${selectedUsers.size} selected users?`)) {
        performBulkAction('verify');
    }
}

function bulkSuspend() {
    if (confirm(`Suspend ${selectedUsers.size} selected users?`)) {
        performBulkAction('suspend');
    }
}

function bulkDelete() {
    if (confirm(`Delete ${selectedUsers.size} selected users? This action cannot be undone.`)) {
        performBulkAction('delete');
    }
}

function performBulkAction(action) {
    fetch('/admin/users/bulk-action', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            action: action,
            users: Array.from(selectedUsers)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to perform bulk action. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
    
    closeBulkModal();
}

function resendVerification(userId) {
    if (confirm('Resend verification email to this user?')) {
        fetch(`/admin/users/${userId}/resend-verification`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Verification email sent successfully.');
            } else {
                alert('Failed to send verification email.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

function suspendUser(userId) {
    if (confirm('Suspend this user? They will not be able to log in.')) {
        fetch(`/admin/users/${userId}/suspend`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to suspend user.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

// Auto-refresh stats every 60 seconds
setInterval(() => {
    fetch('/admin/users/stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-users').textContent = data.total_users;
            document.getElementById('active-users').textContent = data.active_users;
            document.getElementById('pending-users').textContent = data.pending_users;
            document.getElementById('admin-users').textContent = data.admin_users;
        })
        .catch(error => {
            console.warn('Failed to refresh stats:', error);
        });
}, 60000);
</script>
@endsection
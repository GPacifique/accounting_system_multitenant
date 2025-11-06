{{-- resources/views/admin/tenants/users.blade.php --}}
@extends('layouts.app')

@section('title', 'Users for ' . $tenant->name . ' - Tenant Management | SiteLedger')
@section('meta_description', 'Manage users for tenant ' . $tenant->name . ' including viewing user roles, permissions, and tenant membership details.')
@section('meta_keywords', 'tenant users, user management, role management, tenant administration, user permissions')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center mb-2">
                    <div class="theme-aware-bg-card/20 rounded-lg p-2 mr-4">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">Users for {{ $tenant->name }}</h1>
                        <p class="text-blue-100">{{ $tenant->domain }}.{{ config('app.domain', 'siteledger.com') }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 text-sm">
                    <span class="theme-aware-bg-card/20 px-3 py-1 rounded-full">{{ $users->total() }} Total Users</span>
                    <span class="bg-blue-500/20 px-3 py-1 rounded-full">{{ ucfirst($tenant->subscription_plan) }} Plan</span>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.tenants.show', $tenant) }}" class="theme-aware-bg-card text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
                    <i class="fas fa-building mr-2"></i>
                    Tenant Details
                </a>
                <a href="{{ route('admin.tenants.index') }}" class="bg-blue-500/30 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-500/50 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Tenants
                </a>
            </div>
        </div>
    </div>

    {{-- Users List --}}
    <div class="theme-aware-bg-card rounded-xl shadow-lg">
        <div class="p-6 border-b theme-aware-border">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold theme-aware-text">Tenant Users</h2>
                    <p class="theme-aware-text-secondary mt-1">Users with access to {{ $tenant->name }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <input type="text" placeholder="Search users..." class="pl-10 pr-4 py-2 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search theme-aware-text-muted"></i>
                        </div>
                    </div>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-user-plus mr-2"></i>
                        Invite User
                    </button>
                </div>
            </div>
        </div>

        @if($users->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="theme-aware-bg-secondary">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">Tenant Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">Admin Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-3 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">Last Activity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="theme-aware-bg-card divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:theme-aware-bg-secondary">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium theme-aware-text">{{ $user->name }}</div>
                                    <div class="text-sm theme-aware-text-muted">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-xs font-medium rounded-full 
                                {{ $user->pivot->role === 'admin' ? 'bg-purple-100 text-purple-800' : 
                                   ($user->pivot->role === 'manager' ? 'bg-blue-100 text-blue-800' : 
                                   ($user->pivot->role === 'accountant' ? 'bg-green-100 text-green-800' : 'theme-aware-bg-secondary theme-aware-text')) }}">
                                {{ ucfirst($user->pivot->role ?? 'User') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->pivot->is_admin)
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                    <i class="fas fa-crown mr-1"></i>
                                    Admin
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium theme-aware-bg-secondary theme-aware-text-secondary rounded-full">
                                    Member
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm theme-aware-text-muted">
                            {{ $user->pivot->created_at ? $user->pivot->created_at->format('M j, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm theme-aware-text-muted">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900" title="View User">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$user->pivot->is_admin)
                                <button type="button" class="text-red-600 hover:text-red-900" title="Remove from Tenant" onclick="removeUser({{ $user->id }})">
                                    <i class="fas fa-user-times"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-6 py-4 border-t theme-aware-border">
            {{ $users->links() }}
        </div>
        @endif

        @else
        {{-- No Users State --}}
        <div class="text-center py-12">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 theme-aware-bg-secondary rounded-full flex items-center justify-center">
                    <i class="fas fa-users theme-aware-text-muted text-2xl"></i>
                </div>
            </div>
            <h3 class="text-lg font-medium theme-aware-text mb-2">No Users Found</h3>
            <p class="theme-aware-text-muted mb-6">This tenant doesn't have any users yet.</p>
            <button class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-user-plus mr-2"></i>
                Invite First User
            </button>
        </div>
        @endif
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
        <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-500 text-white mr-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold theme-aware-text">{{ $users->total() }}</p>
                    <p class="theme-aware-text-secondary">Total Users</p>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-500 text-white mr-4">
                    <i class="fas fa-crown text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold theme-aware-text">{{ $users->where('pivot.is_admin', true)->count() }}</p>
                    <p class="theme-aware-text-secondary">Admins</p>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-500 text-white mr-4">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold theme-aware-text">{{ $users->where('last_login_at', '!=', null)->count() }}</p>
                    <p class="theme-aware-text-secondary">Active Users</p>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-500 text-white mr-4">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold theme-aware-text">{{ $users->where('last_login_at', null)->count() }}</p>
                    <p class="theme-aware-text-secondary">Pending</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Remove User Modal (placeholder) --}}
<div id="removeUserModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg theme-aware-bg-card">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium theme-aware-text mb-2">Remove User from Tenant</h3>
            <p class="text-sm theme-aware-text-muted mb-6">Are you sure you want to remove this user from the tenant? This action cannot be undone.</p>
            <div class="flex justify-center space-x-3">
                <button type="button" class="px-4 py-2 bg-gray-300 theme-aware-text-secondary rounded-lg hover:bg-gray-400 transition" onclick="closeRemoveModal()">
                    Cancel
                </button>
                <button type="button" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition" onclick="confirmRemoveUser()">
                    Remove User
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let userToRemove = null;

function removeUser(userId) {
    userToRemove = userId;
    document.getElementById('removeUserModal').classList.remove('hidden');
}

function closeRemoveModal() {
    document.getElementById('removeUserModal').classList.add('hidden');
    userToRemove = null;
}

function confirmRemoveUser() {
    if (userToRemove) {
        // Here you would make an AJAX request to remove the user
        console.log('Removing user:', userToRemove);
        closeRemoveModal();
        // For now, just show a success message
        alert('User removal functionality would be implemented here.');
    }
}

// Close modal when clicking outside
document.getElementById('removeUserModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRemoveModal();
    }
});
</script>
@endsection
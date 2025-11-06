@extends('layouts.app')
@section('title', 'User Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <a href="{{ route('users.index') }}" class="btn-secondary inline-flex mb-3">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Users
                </a>
                <h2 class="text-3xl font-bold theme-aware-text leading-tight flex items-center gap-2">
                    <i class="fas fa-user-circle theme-aware-text-muted"></i> User Details
                </h2>
            </div>
            <div class="flex items-center gap-2 mt-4 sm:mt-0">
                <a href="{{ route('users.edit', $user) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>Edit User & Roles
                </a>
                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button class="btn-danger">
                        <i class="fas fa-trash mr-2"></i>Delete User
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- User Info Card -->
            <div class="lg:col-span-2 theme-aware-bg-card rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 sm:p-8">
                    <h5 class="text-lg font-semibold theme-aware-text-secondary mb-4">Basic Information</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm theme-aware-text-muted">Full Name</label>
                            <p class="mt-1 theme-aware-text flex items-center gap-2">
                                <i class="fas fa-user text-indigo-500"></i>{{ $user->name }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm theme-aware-text-muted">Email Address</label>
                            <p class="mt-1 theme-aware-text flex items-center gap-2">
                                <i class="fas fa-envelope text-indigo-500"></i>
                                <a href="mailto:{{ $user->email }}" class="text-indigo-600 hover:underline">{{ $user->email }}</a>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm theme-aware-text-muted">User ID</label>
                            <p class="mt-1 theme-aware-text flex items-center gap-2">
                                <i class="fas fa-hashtag theme-aware-text-muted"></i>{{ $user->id }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm theme-aware-text-muted">Member Since</label>
                            <p class="mt-1 theme-aware-text flex items-center gap-2">
                                <i class="fas fa-calendar theme-aware-text-muted"></i>{{ $user->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>

                    <!-- Assigned Roles -->
                    <div class="mt-8">
                        <h5 class="text-lg font-semibold theme-aware-text-secondary mb-3 flex items-center gap-2">
                            <i class="fas fa-user-shield theme-aware-text-secondary"></i> Assigned Roles
                        </h5>
                        @if($user->roles->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($user->roles as $role)
                                    <div class="p-3 border rounded theme-aware-bg-secondary">
                                        <div class="flex items-center justify-between">
                                            <h6 class="font-medium">
                                                <i class="fas fa-check-circle text-green-500 mr-2"></i>{{ ucfirst($role->name) }}
                                            </h6>
                                            <span class="text-xs theme-aware-text-muted">{{ $role->permissions->count() }} perms</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded text-yellow-800">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <strong>No Roles Assigned</strong> — This user has no roles and cannot access the system.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h6 class="font-semibold theme-aware-text-secondary mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle theme-aware-text-secondary"></i> Status
                        </h6>
                        <div class="mb-3">
                            <small class="theme-aware-text-muted block">Account Status</small>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-700">
                                    <i class="fas fa-circle mr-1 text-[8px]"></i> Active
                                </span>
                            </p>
                        </div>
                        <div>
                            <small class="theme-aware-text-muted block">Number of Roles</small>
                            <p class="mt-1 font-semibold">{{ $user->roles->count() }} Role(s)</p>
                        </div>
                    </div>
                </div>

                @if($user->roles->count() > 0)
                <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h6 class="font-semibold theme-aware-text-secondary mb-4 flex items-center gap-2">
                            <i class="fas fa-lock theme-aware-text-secondary"></i> Effective Permissions
                        </h6>
                        <div class="max-h-80 overflow-y-auto">
                            @php $permissions = $user->roles->pluck('permissions')->flatten()->unique(); @endphp
                            @if($permissions->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($permissions as $perm)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs theme-aware-bg-secondary theme-aware-text-secondary">{{ $perm->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <small class="theme-aware-text-muted">No permissions assigned through roles</small>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary { @apply inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150; }
    .btn-secondary { @apply inline-flex items-center px-4 py-2 theme-aware-bg-tertiary border theme-aware-border rounded-md font-semibold text-xs theme-aware-text-secondary uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:theme-aware-border-secondary focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150; }
    .btn-danger { @apply inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150; }
</style>
@endpush

@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="container-fluid py-4">
    {{-- Role Check: Admin Only --}}
    @unless(auth()->user()->hasRole('admin'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> You do not have permission to access this page.
        </div>
        @php
            abort(403, 'Unauthorized access');
        @endphp
    @endunless

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-user-cog me-2"></i> Manage Users</h2>
            <p class="text-muted mb-0">Total Users: <strong>{{ $users->total() }}</strong></p>
        </div>
        @can('create', App\Models\User::class)
            <a href="{{ route('users.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Create New User
            </a>
        @endcan
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Users Table Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">User List</h5>
        </div>
        <div class="card-body table-responsive">
            @forelse($users as $user)
                <div class="mb-3 p-3 border rounded">
                    <div class="row align-items-start">
                        <div class="col-md-4">
                            <h6 class="mb-1">{{ $user->name }}</h6>
                            <small class="text-muted d-block">{{ $user->email }}</small>
                            <small class="text-muted d-block">ID: {{ $user->id }}</small>
                        </div>
                        <div class="col-md-4">
                            <strong>Roles:</strong>
                            @if($user->roles->count() > 0)
                                <div class="mt-2">
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-primary me-1 mb-1">
                                            <i class="fas fa-user-shield me-1"></i>{{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <small class="text-muted">No roles assigned</small>
                            @endif
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info" title="View Details">
                                <i class="fas fa-eye me-1"></i> View
                            </a>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning" title="Edit User & Roles">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Are you sure you want to delete this user?');"
                                        title="Delete User">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No users found.</p>
                    <a href="{{ route('users.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Create First User
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="card-footer bg-light">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

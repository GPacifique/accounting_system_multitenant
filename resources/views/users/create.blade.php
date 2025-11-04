@extends('layouts.app')

@section('title', 'Create New User')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Users
        </a>
        <h2><i class="fas fa-user-plus me-2"></i> Create New User</h2>
    </div>

    <div class="row">
        <!-- Form Column -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user me-2"></i> Full Name
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   required
                                   placeholder="Enter full name">
                            @error('name') 
                                <div class="invalid-feedback d-block">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-envelope me-2"></i> Email Address
                            </label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   required
                                   placeholder="Enter email (must be unique)">
                            @error('email') 
                                <div class="invalid-feedback d-block">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-lock me-2"></i> Password
                            </label>
                            <input type="password" 
                                   name="password" 
                                   class="form-control @error('password') is-invalid @enderror"
                                   required
                                   placeholder="Enter secure password">
                            <small class="form-text text-muted d-block mt-1">
                                Minimum 8 characters
                            </small>
                            @error('password') 
                                <div class="invalid-feedback d-block">{{ $message }}</div> 
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-lock me-2"></i> Confirm Password
                            </label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   class="form-control"
                                   required
                                   placeholder="Confirm password">
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" data-loading-text="Creating...">
                                <i class="fas fa-save me-1"></i> Create User
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Roles Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-user-shield me-2"></i> Assign Roles
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST" id="rolesForm">
                        @csrf

                        <div class="mb-2">
                            <small class="text-muted">Select one or more roles for this user:</small>
                        </div>

                        <div class="roles-list">
                            @forelse($roles as $role)
                                <div class="form-check mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="roles[]" 
                                           value="{{ $role }}"
                                           id="role_{{ $role }}"
                                           {{ in_array($role, old('roles', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label cursor-pointer" for="role_{{ $role }}">
                                        <span class="fw-bold">{{ ucfirst($role) }}</span>
                                        @switch($role)
                                            @case('admin')
                                                <br><small class="text-muted">Full system access & management</small>
                                                @break
                                            @case('manager')
                                                <br><small class="text-muted">Project & employee management</small>
                                                @break
                                            @case('accountant')
                                                <br><small class="text-muted">Financial records & reporting</small>
                                                @break
                                            @default
                                                <br><small class="text-muted">{{ ucfirst($role) }} access</small>
                                        @endswitch
                                    </label>
                                </div>
                            @empty
                                <div class="alert alert-warning" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No roles available. <a href="{{ route('roles.create') }}">Create roles first</a>.
                                </div>
                            @endforelse
                        </div>

                        @error('roles') 
                            <div class="alert alert-danger mt-2">{{ $message }}</div> 
                        @enderror
                    </form>
                </div>

                <!-- Role Information Card -->
                <div class="card-footer bg-light">
                    <small class="fw-bold text-muted d-block mb-2">Role Guide:</small>
                    <ul class="mb-0 ps-3">
                        <li><strong>Admin:</strong> Full system control & management</li>
                        <li><strong>Manager:</strong> Project & employee management</li>
                        <li><strong>Accountant:</strong> Financial records & reporting</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

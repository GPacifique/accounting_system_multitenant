@extends('layouts.app')

@section('title', 'Edit User & Assign Roles')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Users
        </a>
        <h2><i class="fas fa-user-edit me-2"></i> Edit User</h2>
    </div>

    <div class="row">
        <!-- Form Column -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user me-2"></i> Full Name
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
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
                                   value="{{ old('email', $user->email) }}" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   required
                                   placeholder="Enter email">
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
                                   placeholder="Leave blank to keep current password">
                            <small class="form-text text-muted d-block mt-1">
                                Only enter if you want to change the password
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
                                   placeholder="Confirm password">
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Save Changes
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
                    <form action="{{ route('users.update', $user) }}" method="POST" id="rolesForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">

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
                                           {{ in_array($role, old('roles', $userRoles)) ? 'checked' : '' }}>
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

                        @if($roles->count() > 0)
                            <button type="submit" class="btn btn-primary w-100 mt-3">
                                <i class="fas fa-sync me-1"></i> Update Roles
                            </button>
                        @endif
                    </form>
                </div>

                <!-- Current Roles Info -->
                <div class="card-footer bg-light">
                    <small class="text-muted d-block mb-2">Current Roles:</small>
                    @if($user->roles->count() > 0)
                        <div>
                            @foreach($user->roles as $role)
                                <span class="badge bg-success me-1 mb-1">
                                    <i class="fas fa-check me-1"></i>{{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted">No roles assigned</span>
                    @endif
                </div>
            </div>

            <!-- Role Information Card -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> About Roles
                    </h6>
                </div>
                <div class="card-body">
                    <small>
                        <p class="mb-2">Roles control what users can access and do in the system:</p>
                        <ul class="mb-0">
                            <li><strong>Admin:</strong> Full system control</li>
                            <li><strong>Manager:</strong> Manage projects & team</li>
                            <li><strong>Accountant:</strong> Manage finances</li>
                        </ul>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

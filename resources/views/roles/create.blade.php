@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fa-solid fa-plus me-2"></i> Create Role</h2>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Role name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Permissions</label>
                    <div class="row">
                        @forelse($permissions as $permission)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                           value="{{ $permission->name }}" id="perm_{{ $permission->id }}"
                                           {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-muted">No permissions available.</div>
                        @endforelse
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-success" type="submit" data-loading-text="Creating...">Create</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

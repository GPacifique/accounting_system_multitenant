@extends('layouts.app')

@section('title', 'Roles')

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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fa-solid fa-user-shield me-2"></i> Roles</h2>
        @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> New Role
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Permissions</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>{{ $role->name }}</td>
                            <td style="max-width:40%;">
                                @if($role->permissions->count())
                                    @foreach($role->permissions as $perm)
                                        <span class="badge bg-secondary me-1 mb-1">{{ $perm->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">No permissions</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this role?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

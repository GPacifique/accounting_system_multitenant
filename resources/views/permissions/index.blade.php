@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Role Check: Admin Only --}}
    @unless(auth()->user()->hasRole('admin'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> You do not have permission to access this page.
        </div>
        @php
            abort(403, 'Unauthorized access');
        @endphp
    @endunless

    <h2 class="mb-4">Permissions</h2>

    @if(auth()->user()->hasRole('admin'))
        <a href="{{ route('permissions.create') }}" class="btn btn-primary mb-3">Add Permission</a>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Guard</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($permissions as $permission)
            <tr>
                <td>{{ $permission->id }}</td>
                <td>{{ $permission->name }}</td>
                <td>{{ $permission->guard_name }}</td>
                <td>
                    <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this permission?')">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center">No permissions found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection

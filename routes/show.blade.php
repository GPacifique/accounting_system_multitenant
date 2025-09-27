@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 max-w-lg">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">User Details</h1>
        <a href="{{ route('users.index') }}" class="btn bg-gray-400 hover:bg-gray-500">Back to List</a>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <div class="mb-4">
            <strong class="block text-gray-700">Name:</strong>
            <p>{{ $user->name }}</p>
        </div>

        <div class="mb-4">
            <strong class="block text-gray-700">Email:</strong>
            <p>{{ $user->email }}</p>
        </div>

        <div class="mb-6">
            <strong class="block text-gray-700">Roles:</strong>
            @forelse($user->getRoleNames() as $role)
                <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    {{ $role }}
                </span>
            @empty
                <p class="text-gray-500">No roles assigned.</p>
            @endforelse
        </div>

        <div class="border-t pt-4">
            <a href="{{ route('users.edit', $user->id) }}" class="btn bg-blue-500 hover:bg-blue-600">Edit User</a>
        </div>
    </div>
</div>
@endsection
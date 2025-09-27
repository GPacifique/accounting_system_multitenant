@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 max-w-md">
    <h1 class="text-2xl font-bold mb-4">Edit User: {{ $user->name }}</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.update', $user->id) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block mb-1">Name</label>
            <input type="text" name="name" id="name" class="w-full border px-3 py-2 rounded" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="mb-4">
            <label for="email" class="block mb-1">Email</label>
            <input type="email" name="email" id="email" class="w-full border px-3 py-2 rounded" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="mb-4">
            <label for="password" class="block mb-1">Password</label>
            <input type="password" name="password" id="password" class="w-full border px-3 py-2 rounded">
            <small class="text-gray-500">Leave blank to keep current password.</small>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border px-3 py-2 rounded">
        </div>

        <div class="mb-6">
            <label class="block mb-2 font-bold">Roles</label>
            <div class="grid grid-cols-2 gap-2">
                @foreach($roles as $role)
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="roles[]" value="{{ $role }}" class="form-checkbox h-5 w-5 text-blue-600"
                                {{ in_array($role, $userRoles) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">{{ ucfirst($role) }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <button type="submit" class="btn bg-blue-500 hover:bg-blue-600">Update User</button>
            <a href="{{ route('users.index') }}" class="btn bg-gray-400 hover:bg-gray-500 ml-2">Cancel</a>
        </div>
    </form>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-semibold mb-6">Your Profile</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" class="theme-aware-bg-card p-6 rounded shadow">
        @csrf
        @method('PATCH')

        <div class="mb-4">
            <label for="name" class="block theme-aware-text-secondary font-medium mb-1">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300">
            @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block theme-aware-text-secondary font-medium mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300">
            @error('email')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block theme-aware-text-secondary font-medium mb-1">New Password</label>
            <input type="password" name="password" id="password"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300">
            @error('password')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block theme-aware-text-secondary font-medium mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-300">
        </div>

        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Update Profile</button>
    </form>
</div>
@endsection

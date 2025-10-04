@extends('layouts.app')

@section('title', 'Client Details')
@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">{{ $client->name }}</h1>
        <div class="flex items-center gap-2">
            <!-- Edit -->
            <a href="{{ route('clients.edit', $client->id) }}" 
               class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                Edit
            </a>

            <!-- Delete -->
            <form action="{{ route('clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this client?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                    Delete
                </button>
            </form>

            <!-- Back -->
            <a href="{{ route('clients.index') }}" 
               class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600">
                Back
            </a>
        </div>
    </div>

    <!-- Client details -->
    <div class="bg-white shadow rounded-lg p-6">
        <p><strong>Contact Person:</strong> {{ $client->contact_person }}</p>
        <p><strong>Email:</strong> {{ $client->email ?? 'N/A' }}</p>
        <p><strong>Phone:</strong> {{ $client->phone }}</p>
        <p><strong>Address:</strong> {{ $client->address ?? 'N/A' }}</p>
        <p><strong>Created At:</strong> {{ $client->created_at->format('d M Y, H:i') }}</p>
        <p><strong>Updated At:</strong> {{ $client->updated_at->format('d M Y, H:i') }}</p>
    </div>
</div>
@endsection

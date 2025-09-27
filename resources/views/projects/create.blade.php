{{-- resources/views/projects/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Project')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Create Project</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
            <strong class="block mb-2">Please fix the errors below:</strong>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li class="text-sm text-red-700">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('projects.store') }}" method="POST">
        @csrf

        {{-- Client select --}}
        <div class="mb-4">
            <label for="client_id" class="block text-sm font-medium mb-1">Client</label>
            <select id="client_id" name="client_id" class="w-full border rounded px-3 py-2" required>
                <option value="">-- select client --</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Project name --}}
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium mb-1">Project name</label>
            <input id="name" name="name" value="{{ old('name') }}"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        {{-- Dates --}}
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="start_date" class="block text-sm font-medium mb-1">Start date</label>
                <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium mb-1">End date</label>
                <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}"
                       class="w-full border rounded px-3 py-2">
            </div>
        </div>

        {{-- Contract value --}}
        <div class="mb-4">
            <label for="contract_value" class="block text-sm font-medium mb-1">Contract Value</label>
            <input id="contract_value" name="contract_value" type="number" step="0.01" value="{{ old('contract_value', 0) }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                Save Project
            </button>
            <a href="{{ route('projects.index') }}" class="text-sm text-gray-600">Cancel</a>
        </div>
    </form>
</div>
@endsection

{{-- resources/views/projects/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">✏️ Edit Project</h1>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-4">
            <ul class="list-disc pl-6">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Back Button --}}
    <a href="{{ route('projects.index') }}" 
       class="inline-block mb-4 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
       ⬅ Back to Projects
    </a>

    {{-- Edit Project Form --}}
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <form action="{{ route('projects.update', $project->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Project Name --}}
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium">Project Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $project->name) }}"
                       class="w-full mt-2 p-2 border rounded-lg focus:ring focus:ring-blue-300"
                       required>
            </div>

            {{-- Client --}}
            <div class="mb-4">
                <label for="client" class="block text-gray-700 font-medium">Client</label>
                <input type="text" id="client" name="client" value="{{ old('client', $project->client) }}"
                       class="w-full mt-2 p-2 border rounded-lg focus:ring focus:ring-blue-300"
                       required>
            </div>

            {{-- Status --}}
            <div class="mb-4">
                <label for="status" class="block text-gray-700 font-medium">Status</label>
                <select id="status" name="status"
                        class="w-full mt-2 p-2 border rounded-lg focus:ring focus:ring-blue-300"
                        required>
                    <option value="Ongoing" {{ old('status', $project->status) == 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="Completed" {{ old('status', $project->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Pending" {{ old('status', $project->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            {{-- Deadline --}}
            <div class="mb-4">
                <label for="deadline" class="block text-gray-700 font-medium">Deadline</label>
                <input type="date" id="deadline" name="deadline" value="{{ old('deadline', $project->deadline) }}"
                       class="w-full mt-2 p-2 border rounded-lg focus:ring focus:ring-blue-300"
                       required>
            </div>

            {{-- Budget --}}
            <div class="mb-4">
                <label for="budget" class="block text-gray-700 font-medium">Budget ($)</label>
                <input type="number" step="0.01" id="budget" name="budget" value="{{ old('budget', $project->budget) }}"
                       class="w-full mt-2 p-2 border rounded-lg focus:ring focus:ring-blue-300">
            </div>

            {{-- Submit Button --}}
            <div class="mt-6">
                <button type="submit" 
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 shadow">
                    💾 Update Project
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

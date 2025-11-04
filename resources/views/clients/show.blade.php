@extends('layouts.app')

@section('title', 'Client Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold theme-aware-text leading-tight">{{ $client->name }}</h1>
                <p class="text-sm theme-aware-text-muted mt-1">
                    Client since {{ $client->created_at->format('F Y') }}
                </p>
            </div>
            <div class="flex items-center gap-2 mt-4 sm:mt-0">
                <a href="{{ route('clients.edit', $client->id) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <form action="{{ route('clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this client?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">
                        <i class="fas fa-trash-alt mr-2"></i>Delete
                    </button>
                </form>
                <a href="{{ route('clients.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Clients
                </a>
            </div>
        </div>

        {{-- Client Details Card --}}
        <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Contact Information --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Contact Information</h3>
                        <dl class="space-y-4">
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Contact Person</dt>
                                <dd class="mt-1 text-md text-gray-900">{{ $client->contact_person }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Email Address</dt>
                                <dd class="mt-1 text-md text-gray-900">
                                    @if($client->email)
                                        <a href="mailto:{{ $client->email }}" class="text-indigo-600 hover:underline">{{ $client->email }}</a>
                                    @else
                                        <span class="theme-aware-text-muted">N/A</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Phone Number</dt>
                                <dd class="mt-1 text-md text-gray-900">{{ $client->phone }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Address</dt>
                                <dd class="mt-1 text-md text-gray-900">{{ $client->address ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Metadata --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">System Information</h3>
                        <dl class="space-y-4">
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Client ID</dt>
                                <dd class="mt-1 text-md text-gray-900">#{{ str_pad($client->id, 5, '0', STR_PAD_LEFT) }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Date Created</dt>
                                <dd class="mt-1 text-md text-gray-900">{{ $client->created_at->format('d M Y, H:i A') }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Last Updated</dt>
                                <dd class="mt-1 text-md text-gray-900">{{ $client->updated_at->format('d M Y, H:i A') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Associated Projects/Invoices (Example) --}}
        <div class="mt-8">
            <h2 class="text-2xl font-bold theme-aware-text mb-4">Associated Projects</h2>
            <div class="theme-aware-bg-card rounded-xl shadow-lg p-6">
                <p class="theme-aware-text-muted">Project history will be displayed here.</p>
                {{-- You can loop through $client->projects here if the relationship is set up --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-primary {
        @apply inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    .btn-danger {
        @apply inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    .btn-secondary {
        @apply inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
</style>
@endpush

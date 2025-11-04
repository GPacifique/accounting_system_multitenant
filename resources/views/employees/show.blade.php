@extends('layouts.app')

@section('title', 'Employee Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold theme-aware-text leading-tight">{{ $employee->name }}</h1>
                <p class="text-sm theme-aware-text-muted mt-1">
                    {{ $employee->position }}
                </p>
            </div>
            <div class="flex items-center gap-2 mt-4 sm:mt-0">
                <a href="{{ route('employees.edit', $employee->id) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">
                        <i class="fas fa-trash-alt mr-2"></i>Delete
                    </button>
                </form>
                <a href="{{ route('employees.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Employees
                </a>
            </div>
        </div>

        {{-- Employee Details Card --}}
        <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Contact Information --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Contact Information</h3>
                        <dl class="space-y-4">
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Full Name</dt>
                                <dd class="mt-1 text-md text-gray-900">{{ $employee->name }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Email Address</dt>
                                <dd class="mt-1 text-md text-gray-900">
                                    @if($employee->email)
                                        <a href="mailto:{{ $employee->email }}" class="text-indigo-600 hover:underline">{{ $employee->email }}</a>
                                    @else
                                        <span class="theme-aware-text-muted">N/A</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Phone Number</dt>
                                <dd class="mt-1 text-md text-gray-900">{{ $employee->phone ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Employment Information --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Employment Details</h3>
                        <dl class="space-y-4">
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Position</dt>
                                <dd class="mt-1 text-md text-gray-900">{{ $employee->position }}</dd>
                            </div>
                            <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Date Hired</dt>
                                <dd class="mt-1 text-md text-gray-900">{{ $employee->created_at->format('d M Y') }}</dd>
                            </div>
                             <div class="flex flex-col">
                                <dt class="text-sm font-medium theme-aware-text-muted">Salary</dt>
                                <dd class="mt-1 text-md text-gray-900">{{ $employee->salary ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
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

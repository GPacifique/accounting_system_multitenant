@extends('layouts.app')
@vite('resources/css/app.css')
@vite('resources/js/app.js')
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="container mx-auto px-4">
    {{-- Role Check: Admin or Manager Only --}}
    @unless(auth()->user()->hasAnyRole(['admin', 'manager']))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-triangle"></i> You do not have permission to access this page.
        </div>
        @php
            abort(403, 'Unauthorized access');
        @endphp
    @endunless

    <h1 class="text-2xl font-bold mb-4">Employees</h1>
    <p class="mb-4">Manage your employees efficiently.</p>

    <div class="mb-4">
        @if(auth()->user()->hasAnyRole(['admin', 'manager']))
            <a href="{{ route('employees.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                Add Employee
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-2 px-4 border-b text-left">ID</th>
                    <th class="py-2 px-4 border-b text-left">First Name</th>
                    <th class="py-2 px-4 border-b text-left">Last Name</th>
                    <th class="py-2 px-4 border-b text-left">Email</th>
                    <th class="py-2 px-4 border-b text-left">Phone</th>
                    <th class="py-2 px-4 border-b text-left">Position</th>
                    <th class="py-2 px-4 border-b text-right">Salary</th>
                    <th class="py-2 px-4 border-b text-left">Date of Joining</th>
                    <th class="py-2 px-4 border-b text-left">Department</th>
                    <th class="py-2 px-4 border-b text-left">Created At</th>
                    <th class="py-2 px-4 border-b text-left">Updated At</th>
                    <th class="py-2 px-4 border-b text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                <tr class="hover:bg-gray-100">
                    <td class="py-2 px-4 border-b">{{ $employee->id }}</td>
                    <td class="py-2 px-4 border-b">{{ $employee->first_name }}</td>
                    <td class="py-2 px-4 border-b">{{ $employee->last_name }}</td>
                    <td class="py-2 px-4 border-b">{{ $employee->email }}</td>
                    <td class="py-2 px-4 border-b">{{ $employee->phone ?? '—' }}</td>
                    <td class="py-2 px-4 border-b">{{ $employee->position ?? '—' }}</td>
                    <td class="py-2 px-4 border-b text-right">RWF {{ number_format($employee->salary ?? 0, 0) }}</td>
                    <td class="py-2 px-4 border-b">
                        {{ $employee->hired_date ? \Carbon\Carbon::parse($employee->hired_date)->toDateString() : 'N/A' }}
                    </td>
                    <td class="py-2 px-4 border-b">{{ $employee->department ?? '—' }}</td>
                    <td class="py-2 px-4 border-b">{{ optional($employee->created_at)->format('Y-m-d H:i') ?? '—' }}</td>
                    <td class="py-2 px-4 border-b">{{ optional($employee->updated_at)->format('Y-m-d H:i') ?? '—' }}</td>
                    <td class="py-2 px-4 border-b text-center">
                        <a href="{{ route('employees.show', $employee) }}" class="text-blue-500 hover:underline">View</a>
                        <span class="mx-1">|</span>
                        <a href="{{ route('employees.edit', $employee) }}" class="text-yellow-500 hover:underline">Edit</a>
                        <span class="mx-1">|</span>
                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this employee?')" class="text-red-500 hover:underline">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    {{-- There are 12 columns above, so colspan=12 --}}
                    <td colspan="12" class="py-4 px-4 text-center theme-aware-text-muted">
                        No employees found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination (if $employees is a paginator) --}}
    @if(method_exists($employees, 'links'))
        <div class="mt-4">
            {{ $employees->links() }}
        </div>
    @endif
</div>
@endsection

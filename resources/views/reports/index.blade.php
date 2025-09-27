@extends('layouts.app')
 @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <main class="flex-1">
       
@section('content')
<h1 class="text-2xl font-bold mb-4">Reports</h1>

<a href="{{ route('reports.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded mb-4 inline-block">Generate Report</a>

@if(session('success'))
<div class="bg-green-100 text-green-800 p-2 mb-4 rounded">{{ session('success') }}</div>
@endif

<table class="w-full border">
    <thead>
        <tr>
            <th class="border px-2 py-1">Title</th>
            <th class="border px-2 py-1">Type</th>
            <th class="border px-2 py-1">Date</th>
            <th class="border px-2 py-1">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reports as $report)
        <tr>
            <td class="border px-2 py-1">{{ $report->title }}</td>
            <td class="border px-2 py-1">{{ $report->type }}</td>
            <td class="border px-2 py-1">{{ $report->date }}</td>
            <td class="border px-2 py-1">
                <a href="{{ route('reports.show', $report->id) }}" class="text-blue-500">View</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $reports->links() }}
@endsection

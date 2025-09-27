@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Report Details</h1>

<p><strong>Title:</strong> {{ $report->title }}</p>
<p><strong>Type:</strong> {{ $report->type }}</p>
<p><strong>Date:</strong> {{ $report->date }}</p>
<p><strong>Description:</strong> {{ $report->description }}</p>

<a href="{{ route('reports.index') }}" class="mt-4 inline-block bg-gray-500 text-white px-4 py-2 rounded">Back</a>
@endsection

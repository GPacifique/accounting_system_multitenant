<!-- resources/views/workers/show.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Worker #{{ $worker->id }}</h1>

    <div class="card p-4 mb-4">
        <p><strong>Name:</strong> {{ $worker->full_name }}</p>
        <p><strong>Email:</strong> {{ $worker->email ?? '—' }}</p>
        <p><strong>Phone:</strong> {{ $worker->phone ?? '—' }}</p>
        <p><strong>Position:</strong> {{ $worker->position ?? '—' }}</p>
        <p><strong>Salary:</strong> {{ number_format(($worker->salary_cents ?? 0) / 100, 2) }} {{ $worker->currency }}</p>
        <p><strong>Hired at:</strong> {{ optional($worker->hired_at)->format('F j, Y') ?? '—' }}</p>
        <p><strong>Status:</strong> {{ ucfirst($worker->status) }}</p>

        @if($worker->notes)
            <div class="mt-3">
                <strong>Notes:</strong>
                <div>{!! nl2br(e($worker->notes)) !!}</div>
            </div>
        @endif
    </div>

    <a href="{{ route('workers.edit', $worker) }}" class="btn btn-primary">Edit</a>
    <a href="{{ route('workers.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection

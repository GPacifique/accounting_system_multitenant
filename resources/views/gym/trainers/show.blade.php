@extends('layouts.app')

@section('title', 'Trainer Profile')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">{{ $trainer->first_name }} {{ $trainer->last_name }}</h1>
            <p class="text-muted">{{ $trainer->email }} • {{ $trainer->phone }}</p>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">About</h5>
                    <p class="card-text">{{ $trainer->bio ?? 'No bio available.' }}</p>
                    <p><strong>Experience:</strong> {{ $trainer->experience_years ?? 'N/A' }} years</p>
                    <p><strong>Specializations:</strong>
                        @if($trainer->specializations)
                            @foreach((array)$trainer->specializations as $spec)
                                <span class="badge bg-secondary me-1">{{ ucfirst(str_replace('_',' ', $spec)) }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">None</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-center">
            <img src="{{ $trainer->profile_image ? asset('storage/' . $trainer->profile_image) : 'https://via.placeholder.com/200' }}" class="rounded-circle mb-3" width="180" height="180" style="object-fit:cover;">
            <div>
                <a href="{{ route('gym.trainers.edit', $trainer) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                <form action="{{ route('gym.trainers.destroy', $trainer) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete trainer?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

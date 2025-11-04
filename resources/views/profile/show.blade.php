@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="fas fa-user-circle me-2"></i> My Profile
                </div>
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ Auth::user()->name }}</h5>
                    <p class="mb-2"><strong>Email:</strong> {{ Auth::user()->email }}</p>
                    <p class="mb-2"><strong>Role(s):</strong> {{ Auth::user()->roles->pluck('name')->join(', ') }}</p>
                    <p class="mb-2"><strong>Joined:</strong> {{ Auth::user()->created_at->format('F d, Y') }}</p>
                    <a href="/" class="btn btn-outline-success mt-3"><i class="fas fa-arrow-left me-1"></i> Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

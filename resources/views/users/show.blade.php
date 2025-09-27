@extends('layouts.app')

@section('content')
<div class="container">
    <h1>User Details</h1>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $user->name }}</h5>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Roles:</strong> {{ implode(', ', $user->getRoleNames()->toArray()) }}</p>
        </div>
    </div>

    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">Edit</a>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection

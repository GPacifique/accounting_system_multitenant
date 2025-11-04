@extends('layouts.app')
@section('content')
<div class="text-center p-6">
    <h1 class="text-4xl font-bold text-gray-700 mb-4">404</h1>
    <p class="theme-aware-text-secondary">Page not found.</p>
    <a href="{{ url('/') }}" class="text-blue-600 hover:underline">Go to Home</a>
</div>
@endsection

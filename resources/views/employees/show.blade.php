@extends('layouts.app')
@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<h1>Employee Details</h1>
<p><strong>Name:</strong> {{ $employee->name }}</p>
<p><strong>Email:</strong> {{ $employee->email }}</p>
<p><strong>Position:</strong> {{ $employee->position }}</p>
<a href="{{ route('employees.index') }}">Back to List</a>
@endsection

@extends('layouts.app')

@section('content')
<h1>Edit Employee</h1>

<form action="{{ route('employees.update', $employee->id) }}" method="POST">
    @csrf
    @method('PUT')
    <label>Name:</label>
    <input type="text" name="name" value="{{ $employee->name }}" required>
    <br>
    <label>Email:</label>
    <input type="email" name="email" value="{{ $employee->email }}" required>
    <br>
    <label>Position:</label>
    <input type="text" name="position" value="{{ $employee->position }}">
    <br>
    <button type="submit">Update</button>
</form>
@endsection

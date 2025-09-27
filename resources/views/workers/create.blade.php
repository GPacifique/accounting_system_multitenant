@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Worker</h1>

    <form action="{{ route('workers.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">First name</label>
            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Last name</label>
            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Position</label>
            <input type="text" name="position" class="form-control" value="{{ old('position') }}">
        </div>

        <div class="mb-3 row">
            <div class="col">
                <label class="form-label">Salary</label>
                <input type="number" step="0.01" min="0" name="salary" class="form-control" value="{{ old('salary') }}">
            </div>
            <div class="col">
                <label class="form-label">Currency</label>
                <input type="text" name="currency" class="form-control" value="{{ old('currency', 'USD') }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Hired at</label>
            <input type="date" name="hired_at" class="form-control" value="{{ old('hired_at') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <input type="text" name="status" class="form-control" value="{{ old('status', 'active') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
        </div>

        <button class="btn btn-success">Create</button>
        <a href="{{ route('workers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Worker</h1>

    <form action="{{ route('workers.update', $worker) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">First name</label>
            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $worker->first_name) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Last name</label>
            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $worker->last_name) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $worker->email) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $worker->phone) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Position</label>
            <input type="text" name="position" class="form-control" value="{{ old('position', $worker->position) }}">
        </div>

        <div class="mb-3 row">
            <div class="col">
                <label class="form-label">Salary</label>
                <input type="number" step="0.01" min="0" name="salary" class="form-control" value="{{ old('salary', ($worker->salary_cents ?? 0) / 100) }}">
            </div>
            <div class="col">
                <label class="form-label">Currency</label>
                <input type="text" name="currency" class="form-control" value="{{ old('currency', $worker->currency) }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Hired at</label>
            <input type="date" name="hired_at" class="form-control" value="{{ old('hired_at', optional($worker->hired_at)->format('Y-m-d')) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <input type="text" name="status" class="form-control" value="{{ old('status', $worker->status) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control">{{ old('notes', $worker->notes) }}</textarea>
        </div>

        <button class="btn btn-primary">Save</button>
        <a href="{{ route('workers.show', $worker) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

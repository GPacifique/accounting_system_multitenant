@extends('layouts.app')

@section('title', 'Record Attendance')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Record Attendance</h1>
        <a href="{{ route('gym.attendances.index') }}" class="btn btn-outline-secondary">Back to Records</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('gym.attendances.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="member_id" class="form-label">Member ID or Numeric ID</label>
                    <input id="member_id" name="member_id" value="{{ old('member_id') }}" autofocus
                        class="form-control" placeholder="Scan or type member barcode / id">
                    <div class="form-text">You can scan the member barcode which populates the member_id field.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select">
                        <option value="checkin">Check-in</option>
                        <option value="checkout">Check-out</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes (optional)</label>
                    <textarea id="notes" name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>

                <button class="btn btn-primary">Record Attendance</button>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Attendance Records')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Attendance Records</h1>
        <a href="{{ route('gym.attendances.create') }}" class="btn btn-primary">Record Attendance</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Member</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Notes</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $a)
                        <tr>
                            <td class="text-muted">{{ $a->created_at->format('M j, Y') }}</td>
                            <td>
                                @if($a->member)
                                    {{ $a->member->first_name }} {{ $a->member->last_name }}
                                    <div class="text-muted small">ID: {{ $a->member->member_id ?? $a->member->id }}</div>
                                @else
                                    <span class="text-muted">Unknown member (ID: {{ $a->member_id }})</span>
                                @endif
                            </td>
                            <td>{{ $a->checked_in_at ? $a->checked_in_at->format('H:i:s') : '-' }}</td>
                            <td>{{ $a->checked_out_at ? $a->checked_out_at->format('H:i:s') : '-' }}</td>
                            <td>{{ $a->notes }}</td>
                            <td>{{ $a->creator ? $a->creator->name : 'System' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No attendance records yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

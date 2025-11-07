@extends('layouts.app')

@section('title', 'Gym Reports')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-2">Gym Reports</h1>
            <p class="text-muted">Overview of key gym metrics.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card p-3">
                <div class="card-body">
                    <h6 class="text-muted">Total Members</h6>
                    <div class="stat-value">{{ $totalMembers ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card p-3">
                <div class="card-body">
                    <h6 class="text-muted">Active Members</h6>
                    <div class="stat-value">{{ $activeMembers ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card p-3">
                <div class="card-body">
                    <h6 class="text-muted">New (30d)</h6>
                    <div class="stat-value">{{ $newMembersLast30 ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card p-3">
                <div class="card-body">
                    <h6 class="text-muted">Revenue (This Month)</h6>
                    <div class="stat-value">{{ isset($revenueThisMonth) ? currency($revenueThisMonth) : currency(0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5>Recent Attendances</h5>
                    @if(isset($recentAttendances) && $recentAttendances->count())
                        <ul class="list-group list-group-flush">
                            @foreach($recentAttendances as $att)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $att->member?->full_name ?? 'Unknown' }}</strong>
                                        <div class="small text-muted">{{ $att->member?->member_id ? 'Member ID: '.$att->member->member_id : '' }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div>{{ optional($att->checked_in_at)->format('Y-m-d H:i') }}</div>
                                        <div class="small text-muted">{{ optional($att->checked_out_at)->format('H:i') }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mb-0 text-muted">No recent attendances.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5>Quick Links</h5>
                    <div class="d-flex flex-column">
                        <a href="{{ route('gym.reports.financial') }}" class="btn btn-outline-primary mb-2">Financial Reports</a>
                        <a href="{{ route('gym.reports.membership') }}" class="btn btn-outline-secondary mb-2">Membership Reports</a>
                        <a href="{{ route('gym.reports.attendance') }}" class="btn btn-outline-success">Attendance Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


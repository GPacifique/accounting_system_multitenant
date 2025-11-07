@extends('layouts.app')

@section('title', 'Analytics - GymPro')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line text-primary me-2"></i>
            Analytics & Reports
        </h1>
    </div>

    {{-- Top summary cards --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Revenue</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ isset($financialSummary['total_revenue']) ? number_format($financialSummary['total_revenue'], 2) : (isset($totalRevenue) ? number_format($totalRevenue,2) : '0.00') }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Revenue Today</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $financialSummary['revenue_today'] ?? ($revenueToday ?? '0.00') }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Members</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attendanceStats['total_members'] ?? ($membersCount ?? '0') }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Active Classes</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attendanceStats['active_classes'] ?? ($activeClasses ?? '0') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts and breakdowns --}}
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header">
                    Revenue by Type
                </div>
                <div class="card-body">
                    @if(!empty($revenueByCategory) && is_iterable($revenueByCategory))
                        <ul class="list-group">
                            @foreach($revenueByCategory as $type => $value)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    <span>{{ number_format($value, 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">No revenue breakdown available.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header">Attendance Overview</div>
                <div class="card-body">
                    @if(!empty($attendanceStats) && is_array($attendanceStats))
                        <div class="mb-2">Today: <strong>{{ $attendanceStats['today'] ?? '0' }}</strong></div>
                        <div class="mb-2">This Week: <strong>{{ $attendanceStats['this_week'] ?? '0' }}</strong></div>
                        <div class="mb-2">This Month: <strong>{{ $attendanceStats['this_month'] ?? '0' }}</strong></div>
                    @else
                        <div class="text-muted">No attendance data available.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Equipment and recent revenues --}}
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header">Equipment Status</div>
                <div class="card-body">
                    @if(!empty($equipmentStats) && is_array($equipmentStats))
                        <ul class="list-unstyled">
                            <li>Total: {{ $equipmentStats['total'] ?? '0' }}</li>
                            <li>Operational: {{ $equipmentStats['operational'] ?? '0' }}</li>
                            <li>Under Maintenance: {{ $equipmentStats['maintenance'] ?? '0' }}</li>
                        </ul>
                    @else
                        <div class="text-muted">No equipment data.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header">Recent Revenues</div>
                <div class="card-body">
                    @if(!empty($recentRevenues) && $recentRevenues->count())
                        <ul class="list-group">
                            @foreach($recentRevenues as $r)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ ucfirst(str_replace('_', ' ', $r->revenue_type ?? '')) }}</div>
                                        <div class="small text-muted">{{ $r->transaction_date?->format('Y-m-d') ?? '' }} — {{ $r->description }}</div>
                                    </div>
                                    <div class="fw-bold">{{ number_format($r->amount,2) }}</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">No recent revenues.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

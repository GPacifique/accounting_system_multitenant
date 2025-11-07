@extends('layouts.app')

@section('title', 'Gym Management Dashboard')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">🏋️ Gym Management Dashboard</h1>
            <p class="text-muted">Complete overview of your gym operations</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('gym.analytics') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-line"></i> Analytics
            </a>
            <a href="{{ route('gym.reports') }}" class="btn btn-outline-success">
                <i class="fas fa-file-alt"></i> Reports
            </a>
        </div>
    </div>

    <!-- Key Performance Indicators Row -->
    <div class="row mb-4">
        <!-- Total Members -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Members
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalMembers) }}
                            </div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up"></i> {{ $newMembersThisMonth }} new this month
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Monthly Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ currency($revenueThisMonth) }}
                            </div>
                            <div class="text-xs text-success">
                                <i class="fas fa-dollar-sign"></i> Today: {{ currency($revenueToday) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Classes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Classes Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $classesToday }}
                            </div>
                            <div class="text-xs text-info">
                                <i class="fas fa-dumbbell"></i> {{ $totalBookings }} total bookings
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Equipment Status -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Equipment Status
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $workingEquipment }}/{{ $totalEquipment }}
                            </div>
                            <div class="text-xs {{ $maintenanceNeeded > 0 ? 'text-warning' : 'text-success' }}">
                                <i class="fas fa-tools"></i> {{ $maintenanceNeeded }} need maintenance
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Balances Row: Day / Week / Month / Year -->
    <div class="row mb-4">
        @php
            $balanceCards = [
                ['label' => "Today's Balance", 'value' => $balanceToday ?? 0, 'revenue' => $revenueToday ?? 0, 'expenses' => $expensesToday ?? 0],
                ['label' => "Week-to-date", 'value' => $balanceWeek ?? 0, 'revenue' => $revenueThisWeek ?? 0, 'expenses' => $expensesThisWeek ?? 0],
                ['label' => "Month-to-date", 'value' => $balanceMonth ?? 0, 'revenue' => $revenueThisMonth ?? 0, 'expenses' => $expensesThisMonth ?? 0],
                ['label' => "Year-to-date", 'value' => $balanceYear ?? 0, 'revenue' => $revenueThisYear ?? 0, 'expenses' => $expensesThisYear ?? 0],
            ];
        @endphp

        @foreach($balanceCards as $card)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">{{ $card['label'] }}</div>
                            @php $isPositive = ($card['value'] >= 0); @endphp
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <span class="text-{{ $isPositive ? 'success' : 'danger' }}">{{ currency($card['value']) }}</span>
                            </div>
                            <div class="text-xs text-muted">
                                <small>R: {{ currency($card['revenue']) }} &middot; E: {{ currency($card['expenses']) }}</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue vs Expenses Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue vs Expenses (6 Months)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueExpenseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue by Category -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue by Category</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="revenueCategoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Growth and Class Attendance -->
    <div class="row mb-4">
        <!-- Member Growth Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Member Growth Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="memberGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Trainers -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Trainers by Revenue</h6>
                </div>
                <div class="card-body">
                    @if(!empty($topTrainers) && $topTrainers->count() > 0)
                        @foreach($topTrainers as $trainer)
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <div class="icon-circle bg-primary">
                                    <i class="fas fa-user-tie text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $trainer->first_name }} {{ $trainer->last_name }}</div>
                                <div class="text-muted small">Revenue: {{ currency($trainer->total_revenue ?? 0) }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-user-tie fa-3x mb-3"></i>
                            <p>No trainer revenue data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="row">
        <!-- Recent Members -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Members</h6>
                    <a href="{{ route('gym.members.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentMembers as $member)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-2">
                                                <div class="icon-circle bg-info">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $member->first_name }} {{ $member->last_name }}</div>
                                                <div class="text-muted small">{{ $member->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $member->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($member->status ?? 'Active') }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $member->created_at->format('M j, Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No recent members</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Classes -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Class Schedule</h6>
                    <a href="{{ route('gym.fitness-classes.index') }}" class="btn btn-sm btn-primary">View Schedule</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Time</th>
                                    <th>Bookings</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingClasses as $class)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $class->name }}</div>
                                        <div class="text-muted small">
                                            @if($class->trainer)
                                                {{ $class->trainer->first_name }} {{ $class->trainer->last_name }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-muted small">
                                        {{ \Carbon\Carbon::parse($class->start_time)->format('M j, g:i A') }}
                                    </td>
                                    <td>
                                        <div class="progress progress-sm">
                                            @php
                                                $bookingCount = $class->bookings ?? 0;
                                                $capacity = $class->max_capacity ?? 1;
                                                $percentage = ($bookingCount / $capacity) * 100;
                                            @endphp
                                            <div class="progress-bar bg-info" 
                                                 style="width: {{ min($percentage, 100) }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $bookingCount }}/{{ $capacity }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No upcoming classes</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row">
        <!-- Membership Summary -->
        <div class="col-xl-4 col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Membership Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col">
                            <div class="h5 font-weight-bold text-success">{{ $activeMemberships }}</div>
                            <div class="text-xs text-uppercase text-muted">Active</div>
                        </div>
                        <div class="col">
                            <div class="h5 font-weight-bold text-warning">{{ $expiringMemberships }}</div>
                            <div class="text-xs text-uppercase text-muted">Expiring</div>
                        </div>
                        <div class="col">
                            <div class="h5 font-weight-bold text-info">{{ $totalMemberships }}</div>
                            <div class="text-xs text-uppercase text-muted">Total</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trainer Summary -->
        <div class="col-xl-4 col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Trainer Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col">
                            <div class="h5 font-weight-bold text-success">{{ $activeTrainers }}</div>
                            <div class="text-xs text-uppercase text-muted">Active</div>
                        </div>
                        <div class="col">
                            <div class="h5 font-weight-bold text-info">{{ $totalTrainers }}</div>
                            <div class="text-xs text-uppercase text-muted">Total</div>
                        </div>
                        <div class="col">
                            <div class="h5 font-weight-bold text-primary">{{ $totalClasses }}</div>
                            <div class="text-xs text-uppercase text-muted">Classes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Activity -->
        <div class="col-xl-4 col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Today's Activity</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col">
                            <div class="h5 font-weight-bold text-success">{{ currency($revenueToday) }}</div>
                            <div class="text-xs text-uppercase text-muted">Revenue</div>
                        </div>
                        <div class="col">
                            <div class="h5 font-weight-bold text-info">{{ $bookingsToday }}</div>
                            <div class="text-xs text-uppercase text-muted">Bookings</div>
                        </div>
                        <div class="col">
                            <div class="h5 font-weight-bold text-primary">{{ $classesToday }}</div>
                            <div class="text-xs text-uppercase text-muted">Classes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue vs Expenses Chart
    const revenueExpenseCtx = document.getElementById('revenueExpenseChart').getContext('2d');
    new Chart(revenueExpenseCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($monthlyRevenue) !!},
                borderColor: 'rgba(28, 200, 138, 1)',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.3
            }, {
                label: 'Expenses', 
                data: {!! json_encode($monthlyExpenses) !!},
                borderColor: 'rgba(231, 74, 59, 1)',
                backgroundColor: 'rgba(231, 74, 59, 0.1)',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'RWF ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Revenue by Category Chart
    @if(!empty($revenueByCategory))
    const revenueCategoryCtx = document.getElementById('revenueCategoryChart').getContext('2d');
    new Chart(revenueCategoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($revenueByCategory)) !!},
            datasets: [{
                data: {!! json_encode(array_values($revenueByCategory)) !!},
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    @endif

    // Member Growth Chart
    const memberGrowthCtx = document.getElementById('memberGrowthChart').getContext('2d');
    new Chart(memberGrowthCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'New Members',
                data: {!! json_encode($monthlyMembers) !!},
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-sm {
    height: 0.5rem;
}

.chart-area {
    position: relative;
    height: 10rem;
    width: 100%;
}

.chart-pie {
    position: relative;
    height: 15rem;
    width: 100%;
}
</style>
@endpush
@endsection
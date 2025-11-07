@extends('layouts.app')

@section('title', 'Gym Manager Dashboard')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">🏋️ Gym Manager Dashboard</h1>
            <p class="text-muted">Operational overview and management</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('gym.fitness-classes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Class
            </a>
            <a href="{{ route('gym.members.create') }}" class="btn btn-outline-primary">
                <i class="fas fa-user-plus"></i> Add Member
            </a>
        </div>
    </div>

    <!-- Key Metrics Row -->
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
                                <i class="fas fa-arrow-up"></i> {{ $activeMembers }} active
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Trainers -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Trainers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $activeTrainers }}
                            </div>
                            <div class="text-xs text-info">
                                <i class="fas fa-user-tie"></i> {{ $totalTrainers }} total trainers
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Classes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Today's Classes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $classesToday }}
                            </div>
                            <div class="text-xs text-success">
                                <i class="fas fa-calendar-check"></i> {{ $bookingsToday }} bookings
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
                                {{ $equipmentWorking }} Working
                            </div>
                            <div class="text-xs {{ $equipmentMaintenance > 0 ? 'text-warning' : 'text-success' }}">
                                <i class="fas fa-tools"></i> {{ $equipmentMaintenance }} need maintenance
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

    <!-- Main Content Row -->
    <div class="row">
        <!-- Class Schedule -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Classes</h6>
                    <a href="{{ route('gym.fitness-classes.index') }}" class="btn btn-sm btn-outline-primary">
                        View Full Schedule
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Class Name</th>
                                    <th>Trainer</th>
                                    <th>Time</th>
                                    <th>Bookings</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingClasses as $class)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $class->name }}</div>
                                        <div class="text-muted small">{{ $class->description ?? 'No description' }}</div>
                                    </td>
                                    <td>
                                        @if($class->trainer)
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    <div class="icon-circle bg-success">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold">{{ $class->trainer->first_name }} {{ $class->trainer->last_name }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">No trainer assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ \Carbon\Carbon::parse($class->start_time)->format('g:i A') }}</div>
                                        <div class="text-muted small">{{ \Carbon\Carbon::parse($class->start_time)->format('M j, Y') }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $bookingCount = $class->bookings ?? 0;
                                            $capacity = $class->max_capacity ?? 1;
                                            $percentage = ($bookingCount / $capacity) * 100;
                                        @endphp
                                        <div class="progress mb-1" style="height: 8px;">
                                            <div class="progress-bar bg-info" 
                                                 style="width: {{ min($percentage, 100) }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $bookingCount }}/{{ $capacity }}</small>
                                    </td>
                                    <td>
                                        @if($percentage >= 90)
                                            <span class="badge badge-danger">Full</span>
                                        @elseif($percentage >= 70)
                                            <span class="badge badge-warning">Almost Full</span>
                                        @else
                                            <span class="badge badge-success">Available</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                        <p>No upcoming classes scheduled</p>
                                        <a href="{{ route('gym.fitness-classes.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Schedule a Class
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Popular Classes -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Most Popular Classes</h6>
                </div>
                <div class="card-body">
                    @if(!empty($popularClasses) && count($popularClasses) > 0)
                        @foreach($popularClasses as $class)
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <div class="icon-circle bg-primary">
                                    <i class="fas fa-dumbbell text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $class['name'] }}</div>
                                <div class="text-muted small">{{ $class['bookings'] }} total bookings</div>
                            </div>
                            <div class="text-right">
                                <div class="h6 mb-0 text-primary">{{ $class['bookings'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                            <p>No class data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Content Row -->
    <div class="row">
        <!-- Recent Members -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Members</h6>
                    <a href="{{ route('gym.members.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Action</th>
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
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-toggle="dropdown">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('gym.members.show', $member) }}">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a class="dropdown-item" href="{{ route('gym.members.edit', $member) }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No recent members</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Trainers -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Trainer Management</h6>
                    <a href="{{ route('gym.trainers.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Trainer</th>
                                    <th>Specialization</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTrainers as $trainer)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-2">
                                                <div class="icon-circle bg-success">
                                                    <i class="fas fa-user-tie text-white"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $trainer->first_name }} {{ $trainer->last_name }}</div>
                                                <div class="text-muted small">{{ $trainer->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($trainer->specializations)
                                            @php
                                                $specializations = is_string($trainer->specializations) 
                                                    ? json_decode($trainer->specializations, true) 
                                                    : $trainer->specializations;
                                            @endphp
                                            @if(is_array($specializations))
                                                <div class="text-muted small">
                                                    {{ implode(', ', array_slice($specializations, 0, 2)) }}
                                                    @if(count($specializations) > 2)
                                                        <span class="badge badge-light">+{{ count($specializations) - 2 }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-muted">No specialization</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $trainer->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($trainer->status ?? 'Active') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-toggle="dropdown">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('gym.trainers.show', $trainer) }}">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a class="dropdown-item" href="{{ route('gym.trainers.edit', $trainer) }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No trainers available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards Row -->
    <div class="row">
        <!-- Quick Actions -->
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('gym.fitness-classes.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-plus-circle mb-2"></i><br>
                                Schedule New Class
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('gym.members.create') }}" class="btn btn-success btn-block">
                                <i class="fas fa-user-plus mb-2"></i><br>
                                Add New Member
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('gym.trainers.create') }}" class="btn btn-info btn-block">
                                <i class="fas fa-user-tie mb-2"></i><br>
                                Add New Trainer
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('gym.equipment.index') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-tools mb-2"></i><br>
                                Manage Equipment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

.btn-block i {
    font-size: 1.5rem;
}
</style>
@endpush
@endsection
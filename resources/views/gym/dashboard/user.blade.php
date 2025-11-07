@extends('layouts.app')

@section('title', 'Gym Dashboard')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">🏋️ Gym Dashboard</h1>
            <p class="text-muted">Daily overview and activities</p>
        </div>
    </div>

    <!-- Key Metrics Row -->
    <div class="row mb-4">
        <!-- Total Members -->
        <div class="col-xl-4 col-md-6 mb-4">
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
                            <div class="text-xs text-muted">
                                <i class="fas fa-users"></i> Active gym members
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Classes -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Today's Classes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $classesToday }}
                            </div>
                            <div class="text-xs text-success">
                                <i class="fas fa-dumbbell"></i> Scheduled for today
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('gym.members.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-users"></i> View Members
                        </a>
                        <a href="{{ route('gym.fitness-classes.index') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-calendar"></i> View Schedule
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Classes -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Classes</h6>
                    <a href="{{ route('gym.fitness-classes.index') }}" class="btn btn-sm btn-outline-primary">
                        View Full Schedule
                    </a>
                </div>
                <div class="card-body">
                    @if($upcomingClasses && $upcomingClasses->count() > 0)
                        <div class="row">
                            @foreach($upcomingClasses as $class)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card border-left-info h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    {{ \Carbon\Carbon::parse($class->start_time)->format('M j, g:i A') }}
                                                </div>
                                                <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                    {{ $class->name }}
                                                </div>
                                                @if($class->trainer)
                                                    <div class="text-xs text-muted">
                                                        <i class="fas fa-user-tie"></i> 
                                                        {{ $class->trainer->first_name }} {{ $class->trainer->last_name }}
                                                    </div>
                                                @endif
                                                @if($class->max_capacity)
                                                    <div class="text-xs text-muted">
                                                        <i class="fas fa-users"></i> 
                                                        Capacity: {{ $class->max_capacity }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-calendar-times fa-4x mb-3"></i>
                            <h5>No Upcoming Classes</h5>
                            <p>There are no classes scheduled for today.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.d-grid {
    display: grid;
}

.gap-2 {
    gap: 0.5rem;
}
</style>
@endpush
@endsection
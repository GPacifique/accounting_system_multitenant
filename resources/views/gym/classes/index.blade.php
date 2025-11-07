@extends('layouts.app')

@section('title', 'Fitness Classes - GymPro')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-dumbbell text-primary me-2"></i>
            Fitness Classes Schedule
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('gym.fitness-classes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Schedule New Class
            </a>
        </div>
    </div>

    <!-- Classes Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Classes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">24</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Classes Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">6</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Bookings
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">142</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Avg. Occupancy
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">78%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Classes Schedule -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Today's Schedule</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Time</th>
                            <th>Class</th>
                            <th>Trainer</th>
                            <th>Duration</th>
                            <th>Capacity</th>
                            <th>Bookings</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Sample Class Data -->
                        <tr>
                            <td>
                                <div class="fw-bold">09:00 AM</div>
                                <div class="text-muted small">Nov 7, 2025</div>
                            </td>
                            <td>
                                <div class="fw-bold">Morning Yoga</div>
                                <div class="text-muted small">Beginner Level</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle me-2" src="https://via.placeholder.com/30" width="30" height="30">
                                    <span>Sarah Johnson</span>
                                </div>
                            </td>
                            <td>60 min</td>
                            <td>20</td>
                            <td>
                                <div class="progress mb-1" style="height: 8px;">
                                    <div class="progress-bar bg-info" style="width: 75%"></div>
                                </div>
                                <small class="text-muted">15/20</small>
                            </td>
                            <td>
                                <span class="badge bg-success">Active</span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View Details</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-users me-2"></i>Manage Bookings</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-ban me-2"></i>Cancel Class</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-bold">10:30 AM</div>
                                <div class="text-muted small">Nov 7, 2025</div>
                            </td>
                            <td>
                                <div class="fw-bold">HIIT Training</div>
                                <div class="text-muted small">Intermediate Level</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img class="rounded-circle me-2" src="https://via.placeholder.com/30" width="30" height="30">
                                    <span>Mike Davis</span>
                                </div>
                            </td>
                            <td>45 min</td>
                            <td>15</td>
                            <td>
                                <div class="progress mb-1" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: 93%"></div>
                                </div>
                                <small class="text-muted">14/15</small>
                            </td>
                            <td>
                                <span class="badge bg-warning">Almost Full</span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View Details</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-users me-2"></i>Manage Bookings</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-ban me-2"></i>Cancel Class</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
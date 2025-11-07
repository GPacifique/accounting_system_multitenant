@extends('layouts.app')

@section('title', 'Trainers Management - GymPro')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-tie text-primary me-2"></i>
            Trainers Management
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('gym.trainers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Add New Trainer
            </a>
        </div>
    </div>

    <!-- Trainers Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Trainers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $trainers->total() ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users-cog fa-2x text-gray-300"></i>
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
                                Active Trainers
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Trainer::where('status', 'active')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                                Classes Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\FitnessClass::whereDate('class_date', now())->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
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
                                Average Rating
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format(\App\Models\Trainer::avg('experience_years') ?? 0, 1) }}/5</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trainers Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Trainers List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Trainer</th>
                            <th>Specialization</th>
                            <th>Experience</th>
                            <th>Status</th>
                            <th>Rating</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trainers as $trainer)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        <img class="rounded-circle" src="{{ $trainer->profile_image ? asset('storage/' . $trainer->profile_image) : 'https://via.placeholder.com/40' }}" alt="Avatar" width="40" height="40">
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $trainer->first_name }} {{ $trainer->last_name }}</div>
                                        <div class="text-muted small">{{ $trainer->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($trainer->specializations)
                                    @foreach((array)$trainer->specializations as $spec)
                                        <span class="badge bg-primary">{{ ucfirst(str_replace('_',' ', $spec)) }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $trainer->experience_years ?? '—' }} years</td>
                            <td>
                                <span class="badge bg-{{ $trainer->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($trainer->status ?? 'Active') }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">{{ number_format($trainer->getAverageRatingAttribute(),1) }}</span>
                                    <div class="text-warning">
                                        @for($i=0;$i<5;$i++)<i class="fas fa-star"></i>@endfor
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('gym.trainers.show', $trainer) }}"><i class="fas fa-eye me-2"></i>View Profile</a></li>
                                        <li><a class="dropdown-item" href="{{ route('gym.trainers.edit', $trainer) }}"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-calendar me-2"></i>Schedule</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-chart-line me-2"></i>Performance</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No trainers available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
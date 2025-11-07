@extends('layouts.app')

@section('title')
    Membership Plans - GymPro
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-id-card text-primary me-2"></i>
            Membership Plans
        </h1>
        <a href="{{ route('gym.membership-plans.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Plan
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Available Plans</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @if(isset($plans) && $plans->count())
                    @foreach($plans as $plan)
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">{{ $plan->name }}</h5>
                                        <span class="badge bg-primary">{{ $plan->duration_label ?? ($plan->duration . ' days') }}</span>
                                    </div>
                                    <p class="card-text text-muted mb-2">{{ $plan->benefits }}</p>
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <div class="h5 mb-0">{{ currency($plan->price) }}</div>
                                        <div>
                                            <a href="#" class="btn btn-sm btn-outline-info">View</a>
                                            <a href="#" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- sample fallback cards when no $plans are provided --}}
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">Basic Monthly</h5>
                                    <span class="badge bg-secondary">30 days</span>
                                </div>
                                <p class="card-text text-muted mb-2">Gym access, Locker</p>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <div class="h5 mb-0">{{ currency(30) }}</div>
                                    <div>
                                        <a href="#" class="btn btn-sm btn-outline-info">View</a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">Premium Annual</h5>
                                    <span class="badge bg-secondary">365 days</span>
                                </div>
                                <p class="card-text text-muted mb-2">All access, PT discount</p>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <div class="h5 mb-0">{{ currency(500) }}</div>
                                    <div>
                                        <a href="#" class="btn btn-sm btn-outline-info">View</a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

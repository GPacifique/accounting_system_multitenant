@extends('layouts.app')

@section('title', 'Financial Reports')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Financial Reports</h1>
            <p class="text-muted">Comprehensive financial analysis and reporting</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('finance.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Finance
            </a>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        This page redirects to the main <a href="{{ route('reports.index') }}" class="alert-link">Reports section</a> 
        where you can generate and download comprehensive financial reports.
    </div>

    <div class="text-center py-5">
        <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
        <h3>Redirecting to Reports...</h3>
        <p class="text-muted">You will be redirected to the main reports page in 3 seconds.</p>
        <a href="{{ route('reports.index') }}" class="btn btn-primary">Go to Reports Now</a>
    </div>
</div>

<script>
    setTimeout(function() {
        window.location.href = "{{ route('reports.index') }}";
    }, 3000);
</script>
@endsection
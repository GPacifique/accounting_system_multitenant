@extends('layouts.app')

@section('title', 'New Payment')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fa-solid fa-plus me-2"></i> New Payment</h2>
        <a href="{{ route('payments.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to list
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @include('payments._form')
        </div>
    </div>
</div>
@endsection

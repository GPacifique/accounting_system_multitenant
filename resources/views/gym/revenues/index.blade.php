@extends('layouts.app')

@section('title', 'Gym Revenues - GymPro')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cash-register text-primary me-2"></i>
            Gym Revenues
        </h1>
        <a href="{{ route('gym.gym-revenues.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>
            Add Revenue
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Revenues</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Payment Method</th>
                            <th>Description</th>
                            <th>Receipt</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($revenues) && $revenues->count())
                            @foreach($revenues as $rev)
                                <tr>
                                    <td>{{ $rev->transaction_date->format('Y-m-d') }}</td>
                                    <td>{{ number_format($rev->amount, 2) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $rev->revenue_type)) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $rev->payment_method ?? 'n/a')) }}</td>
                                    <td>{{ $rev->description }}</td>
                                    <td>
                                        @if(!empty($rev->receipt_path))
                                            <a href="{{ asset('storage/'.$rev->receipt_path) }}" target="_blank">View</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-info">View</a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <a href="#" class="btn btn-sm btn-outline-danger">Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <!-- Fallback sample rows -->
                            <tr>
                                <td>2025-11-07</td>
                                <td>120.00</td>
                                <td>Membership</td>
                                <td>Card</td>
                                <td>Monthly membership payment</td>
                                <td>-</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-info">View</a>
                                </td>
                            </tr>
                            <tr>
                                <td>2025-11-06</td>
                                <td>50.00</td>
                                <td>Class Booking</td>
                                <td>Cash</td>
                                <td>Yoga class drop-in</td>
                                <td>-</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-info">View</a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

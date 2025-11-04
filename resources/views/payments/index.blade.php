@extends('layouts.app')

@section('title', 'Payment Tracking System - Worker & Vendor Payments | SiteLedger')
@section('meta_description', 'Comprehensive payment management system for construction companies. Track worker payments, vendor payments, manage payroll, and monitor all outgoing payments with detailed records and reporting.')
@section('meta_keywords', 'payment tracking, worker payments, vendor payments, construction payroll, payment management, outgoing payments, payment records')

@section('content')
<div class="container-fluid py-4">
    {{-- Role Check: Admin or Accountant Only --}}
    @unless(auth()->user()->hasAnyRole(['admin', 'accountant']))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> You do not have permission to access this page.
        </div>
        @php
            abort(403, 'Unauthorized access');
        @endphp
    @endunless

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fa-solid fa-money-bill me-2"></i> Payments</h2>
        <div class="d-flex gap-2">
            {{-- Download Buttons --}}
            <x-download-buttons 
                route="payments.export" 
                filename="payments" 
                size="sm" />
            
            @if(auth()->user()->hasAnyRole(['admin', 'accountant']))
                <a href="{{ route('payments.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus me-1"></i> New Payment
                </a>
            @endif
        </div>
    </div>

    <!-- Flash messages -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Reference</th>
                        <th>Employee</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>{{ $payment->reference ?? '—' }}</td>
                            <td>
                                @if($payment->employee)
                                    {{ $payment->employee->full_name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ ucfirst($payment->method) }}</td>
                            <td>RWF {{ number_format($payment->amount, 2) }}</td>
                            <td>{{ optional($payment->created_at)->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($payment->status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-info">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('payments.edit', $payment) }}" class="btn btn-sm btn-warning">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this payment?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No payments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Transaction Management - Complete Payment History | SiteLedger')
@section('meta_description', 'Comprehensive transaction management for construction companies. Track all financial transactions including revenue, expenses, payroll, and transfers with detailed categorization and reporting.')
@section('meta_keywords', 'transaction management, payment history, financial transactions, construction finance tracking, revenue tracking, expense records, payment records')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 theme-aware-text"><i class="fas fa-money-check-alt text-success me-2"></i>Transactions</h1>
        <div class="d-flex gap-2">
            {{-- Download Buttons --}}
            <x-download-buttons 
                route="transactions.export" 
                filename="transactions" 
                size="sm" />
            
            <a href="{{ route('transactions.create') }}" class="btn btn-success theme-aware-shadow">
                <i class="fas fa-plus me-1"></i> New Transaction
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card theme-aware-shadow mb-4">
        <div class="card-body pb-0">
            <form class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select class="form-select" name="type">
                        <option value="">All Types</option>
                        <option value="revenue">Revenue</option>
                        <option value="expense">Expense</option>
                        <option value="payroll">Payroll</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Statuses</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control" name="date">
                </div>
                <div class="col-md-3 text-end">
                    <button class="btn btn-outline-success"><i class="fas fa-filter me-1"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    @if(isset($transactions) && $transactions->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle theme-aware-shadow">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Reference</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td><span class="badge bg-light text-success">{{ $t->reference ?? '—' }}</span></td>
                            <td>
                                @if($t->type === 'revenue')
                                    <span class="badge bg-success"><i class="fas fa-arrow-down me-1"></i>Revenue</span>
                                @elseif($t->type === 'expense')
                                    <span class="badge bg-danger"><i class="fas fa-arrow-up me-1"></i>Expense</span>
                                @elseif($t->type === 'payroll')
                                    <span class="badge bg-info text-dark"><i class="fas fa-users me-1"></i>Payroll</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($t->type) }}</span>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $t->category ?? '—' }}</span></td>
                            <td><strong class="text-success">{{ number_format($t->amount, 2) }}</strong></td>
                            <td>
                                @if($t->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($t->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($t->status === 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-secondary">—</span>
                                @endif
                            </td>
                            <td>{{ optional($t->created_at)->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <a href="{{ route('transactions.show', $t) }}" class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('transactions.edit', $t) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('transactions.destroy', $t) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this transaction?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $transactions->links() }}
        </div>
    @else
        <div class="alert alert-info">
            No transactions found.
        </div>
    @endif
</div>
@endsection

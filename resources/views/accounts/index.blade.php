@extends('layouts.app')

@section('title', 'Accounts')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-chart-tree-map me-2"></i>
            Accounts Management
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('accounts.export', 'csv') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-download me-1"></i>
                    Export CSV
                </a>
                <a href="{{ route('accounts.export', 'json') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-code me-1"></i>
                    Export JSON
                </a>
            </div>
            <a href="{{ route('accounts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                New Account
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Accounts
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_accounts'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-2x text-gray-300"></i>
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
                                Active Accounts
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['active_accounts'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Type Distribution -->
        @foreach(['asset' => 'Assets', 'liability' => 'Liabilities', 'equity' => 'Equity', 'revenue' => 'Revenue', 'expense' => 'Expenses'] as $type => $label)
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ $label }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['by_type'][$type] ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filters
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('accounts.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="type" class="form-label">Account Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">All Types</option>
                        @foreach($accountTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="is_active" class="form-label">Status</label>
                    <select name="is_active" id="is_active" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Search accounts..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Accounts List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Account Name</th>
                            <th>Type</th>
                            <th>Parent</th>
                            <th>Current Balance</th>
                            <th>Currency</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                        <tr>
                            <td>
                                <code>{{ $account->code }}</code>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($account->parent)
                                        <i class="fas fa-level-up-alt text-muted me-2" title="Sub-account"></i>
                                    @endif
                                    <div>
                                        <strong>{{ $account->name }}</strong>
                                        @if($account->description)
                                            <br><small class="text-muted">{{ Str::limit($account->description, 50) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $account->type === 'asset' ? 'success' : 
                                    ($account->type === 'liability' ? 'warning' : 
                                    ($account->type === 'equity' ? 'info' : 
                                    ($account->type === 'revenue' ? 'primary' : 'danger'))) 
                                }}">
                                    {{ ucfirst($account->type) }}
                                </span>
                            </td>
                            <td>
                                @if($account->parent)
                                    <small>{{ $account->parent->name }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($account->current_balance)
                                    <span class="fw-bold {{ $account->current_balance > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($account->current_balance, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted">0.00</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $account->currency ?? 'USD' }}</small>
                            </td>
                            <td>
                                @if($account->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                                @if($account->is_system)
                                    <span class="badge bg-info ms-1">System</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $account->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('accounts.show', $account) }}" 
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('accounts.edit', $account) }}" 
                                       class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!$account->is_system)
                                    <form action="{{ route('accounts.destroy', $account) }}" method="POST" 
                                          class="d-inline" onsubmit="return confirm('Are you sure you want to delete this account?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                                    <p>No accounts found. <a href="{{ route('accounts.create') }}">Create your first account</a>.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($accounts->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="dataTables_info">
                    Showing {{ $accounts->firstItem() }} to {{ $accounts->lastItem() }} of {{ $accounts->total() }} entries
                </div>
                {{ $accounts->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable features if needed
    $('#dataTable').DataTable({
        "paging": false,
        "searching": false,
        "ordering": true,
        "info": false,
        "responsive": true
    });
});
</script>
@endpush
@endsection
@extends('layouts.app')

@section('title', 'Account Details - ' . $account->name . ' | SiteLedger')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-eye me-2"></i>
            Account Details: {{ $account->name }}
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('accounts.edit', $account) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-1"></i>
                    Edit Account
                </a>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash me-1"></i>
                    Delete
                </button>
            </div>
            <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Accounts
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Account Information Card -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        Account Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Account Code</label>
                                <p class="form-control-plaintext">{{ $account->code }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Account Name</label>
                                <p class="form-control-plaintext">{{ $account->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Account Type</label>
                                <span class="badge bg-primary fs-6">{{ ucfirst($account->type) }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Currency</label>
                                <p class="form-control-plaintext">{{ $account->currency }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Parent Account</label>
                                <p class="form-control-plaintext">
                                    @if($account->parent)
                                        <a href="{{ route('accounts.show', $account->parent) }}" class="text-decoration-none">
                                            {{ $account->parent->name }} ({{ $account->parent->code }})
                                        </a>
                                    @else
                                        <span class="text-muted">None (Root Account)</span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <p class="form-control-plaintext">
                                    @if($account->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                    @if($account->is_system)
                                        <span class="badge bg-warning ms-1">System Account</span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tax Rate</label>
                                <p class="form-control-plaintext">{{ number_format($account->tax_rate * 100, 2) }}%</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Created</label>
                                <p class="form-control-plaintext">{{ $account->created_at->format('M d, Y \a\t H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($account->description)
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <p class="form-control-plaintext">{{ $account->description }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sub-Accounts -->
            @if($account->children && $account->children->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-sitemap me-2"></i>
                        Sub-Accounts ({{ $account->children->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($account->children as $child)
                                <tr>
                                    <td><code>{{ $child->code }}</code></td>
                                    <td>{{ $child->name }}</td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($child->type) }}</span></td>
                                    <td>{{ $child->currency }} {{ number_format($child->current_balance, 2) }}</td>
                                    <td>
                                        @if($child->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('accounts.show', $child) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('accounts.edit', $child) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Statistics Sidebar -->
        <div class="col-lg-4">
            <!-- Balance Information -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calculator me-2"></i>
                        Balance Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Opening Balance</label>
                        <p class="form-control-plaintext text-info fs-5">
                            {{ $account->currency }} {{ number_format($stats['opening_balance'], 2) }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Current Balance</label>
                        <p class="form-control-plaintext text-success fs-4">
                            {{ $account->currency }} {{ number_format($account->current_balance, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Account Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>
                        Account Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Sub-Accounts</span>
                        <span class="badge bg-primary">{{ $stats['children_count'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Has Transactions</span>
                        <span class="badge {{ $stats['has_transactions'] ? 'bg-success' : 'bg-secondary' }}">
                            {{ $stats['has_transactions'] ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Account Level</span>
                        <span class="badge bg-info">
                            {{ $account->parent ? 'Sub-Account' : 'Root Account' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Created By</span>
                        <span class="text-muted">{{ $account->creator->name ?? 'System' }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$account->is_system)
                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>
                                Edit Account
                            </a>
                        @endif
                        
                        @if($account->parent)
                            <a href="{{ route('accounts.show', $account->parent) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-up me-1"></i>
                                View Parent Account
                            </a>
                        @endif
                        
                        <a href="{{ route('accounts.create') }}?parent_id={{ $account->id }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-plus me-1"></i>
                            Add Sub-Account
                        </a>
                        
                        <a href="{{ route('accounts.index') }}" class="btn btn-outline-dark btn-sm">
                            <i class="fas fa-list me-1"></i>
                            All Accounts
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the account <strong>"{{ $account->name }}"</strong>?</p>
                
                @if($stats['has_transactions'])
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Warning:</strong> This account has transactions associated with it. Deleting it may affect your financial records.
                    </div>
                @endif
                
                @if($stats['children_count'] > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This account has {{ $stats['children_count'] }} sub-account(s). You may need to handle them separately.
                    </div>
                @endif
                
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Delete Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-dismiss alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>
@endpush
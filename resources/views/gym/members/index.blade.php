@extends('layouts.app')

@section('title', 'Members Management - GymPro')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users text-primary me-2"></i>
            Members Management
        </h1>
        @php $viewMode = request('view', 'table'); @endphp
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('gym.members.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Add New Member
            </a>

            {{-- View toggle: table or cards --}}
            <div class="btn-group" role="group" aria-label="View toggle">
                <a href="{{ route('gym.members.index', array_merge(request()->except('page'), ['view' => 'table'])) }}" 
                   class="btn btn-sm {{ $viewMode === 'table' ? 'btn-secondary' : 'btn-outline-secondary' }}" title="Table view">
                    <i class="fas fa-table"></i>
                </a>
                <a href="{{ route('gym.members.index', array_merge(request()->except('page'), ['view' => 'cards'])) }}" 
                   class="btn btn-sm {{ $viewMode === 'cards' ? 'btn-secondary' : 'btn-outline-secondary' }}" title="Card view">
                    <i class="fas fa-th-large"></i>
                </a>
            </div>

            <button class="btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-1"></i>
                Print
            </button>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" action="{{ route('gym.members.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search Members</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Name, email, phone..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="membership_type" class="form-label">Membership</label>
                                <select class="form-select" id="membership_type" name="membership_type">
                                    <option value="">All Types</option>
                                    <option value="monthly" {{ request('membership_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ request('membership_type') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="annual" {{ request('membership_type') == 'annual' ? 'selected' : '' }}>Annual</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="sort" class="form-label">Sort By</label>
                                <select class="form-select" id="sort" name="sort">
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="joined_date" {{ request('sort') == 'joined_date' ? 'selected' : '' }}>Join Date</option>
                                    <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>
                                    Filter
                                </button>
                                <a href="{{ route('gym.members.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Members
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">245</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Active Members
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">198</div>
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
                                New This Month
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">23</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x text-gray-300"></i>
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
                                Expiring Soon
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Members List (Table or Card view) -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Members List</h6>
        </div>
        <div class="card-body">
            @if($viewMode === 'table')
                <div class="table-responsive">
                    <table class="table table-hover" id="membersTable">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th>Member</th>
                                <th>Contact</th>
                                <th>Membership</th>
                                <th>Status</th>
                                <th>Join Date</th>
                                <th>Last Visit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Sample Member Rows (replace with dynamic loop when $members provided) -->
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <img class="rounded-circle" src="https://via.placeholder.com/40" alt="Avatar" width="40" height="40">
                                        </div>
                                        <div>
                                            <div class="fw-bold">John Doe</div>
                                            <div class="text-muted small">ID: GYM001</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>john.doe@email.com</div>
                                    <div class="text-muted small">+250 788 123 456</div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">Monthly Premium</span>
                                    <div class="text-muted small">Expires: Dec 15, 2025</div>
                                </td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td>
                                    <div>Jan 15, 2025</div>
                                    <div class="text-muted small">8 months ago</div>
                                </td>
                                <td>
                                    <div>Nov 5, 2025</div>
                                    <div class="text-muted small">2 days ago</div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View Profile</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-credit-card me-2"></i>Membership</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-history me-2"></i>Check-in History</a></li>
                                            <li>
                                                <form method="POST" action="{{ route('gym.attendances.store') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="member_id" value="1">
                                                    <input type="hidden" name="action" value="checkin">
                                                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-in-alt me-2"></i>Check In</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('gym.attendances.store') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="member_id" value="1">
                                                    <input type="hidden" name="action" value="checkout">
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Check Out</button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-warning" href="#"><i class="fas fa-ban me-2"></i>Suspend</a></li>
                                            <li>
                                                <form action="{{ route('gym.members.destroy', 1) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this member?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i>Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="2">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <img class="rounded-circle" src="https://via.placeholder.com/40" alt="Avatar" width="40" height="40">
                                        </div>
                                        <div>
                                            <div class="fw-bold">Jane Smith</div>
                                            <div class="text-muted small">ID: GYM002</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>jane.smith@email.com</div>
                                    <div class="text-muted small">+250 788 654 321</div>
                                </td>
                                <td>
                                    <span class="badge bg-warning">Annual Basic</span>
                                    <div class="text-muted small">Expires: Mar 20, 2026</div>
                                </td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                                <td>
                                    <div>Mar 20, 2025</div>
                                    <div class="text-muted small">6 months ago</div>
                                </td>
                                <td>
                                    <div>Nov 7, 2025</div>
                                    <div class="text-muted small">Today</div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>View Profile</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-credit-card me-2"></i>Membership</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="fas fa-history me-2"></i>Check-in History</a></li>
                                            <li>
                                                <form method="POST" action="{{ route('gym.attendances.store') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="member_id" value="2">
                                                    <input type="hidden" name="action" value="checkin">
                                                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-in-alt me-2"></i>Check In</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('gym.attendances.store') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="member_id" value="2">
                                                    <input type="hidden" name="action" value="checkout">
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>Check Out</button>
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-warning" href="#"><i class="fas fa-ban me-2"></i>Suspend</a></li>
                                            <li>
                                                <form action="{{ route('gym.members.destroy', 2) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this member?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i>Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing 1 to 25 of 245 members
                    </div>
                    <nav aria-label="Members pagination">
                        <ul class="pagination">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @else
                {{-- Card grid view --}}
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="https://via.placeholder.com/56" class="rounded-circle me-3" width="56" height="56" alt="Avatar">
                                    <div>
                                        <div class="fw-bold">John Doe</div>
                                        <div class="text-muted small">ID: GYM001</div>
                                    </div>
                                </div>
                                <p class="mb-2 text-muted small">Monthly Premium — Expires Dec 15, 2025</p>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="#" class="btn btn-sm btn-outline-info">View</a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <form method="POST" action="{{ route('gym.attendances.store') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="member_id" value="1">
                                            <input type="hidden" name="action" value="checkin">
                                            <button type="submit" class="btn btn-sm btn-success ms-1">Check In</button>
                                        </form>
                                        <form method="POST" action="{{ route('gym.attendances.store') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="member_id" value="1">
                                            <input type="hidden" name="action" value="checkout">
                                            <button type="submit" class="btn btn-sm btn-danger ms-1">Check Out</button>
                                        </form>
                                        <form action="{{ route('gym.members.destroy', 1) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Delete this member? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="https://via.placeholder.com/56" class="rounded-circle me-3" width="56" height="56" alt="Avatar">
                                    <div>
                                        <div class="fw-bold">Jane Smith</div>
                                        <div class="text-muted small">ID: GYM002</div>
                                    </div>
                                </div>
                                <p class="mb-2 text-muted small">Annual Basic — Expires Mar 20, 2026</p>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="#" class="btn btn-sm btn-outline-info">View</a>
                                        <a href="#" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        <form method="POST" action="{{ route('gym.attendances.store') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="member_id" value="2">
                                            <input type="hidden" name="action" value="checkin">
                                            <button type="submit" class="btn btn-sm btn-success ms-1">Check In</button>
                                        </form>
                                        <form method="POST" action="{{ route('gym.attendances.store') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="member_id" value="2">
                                            <input type="hidden" name="action" value="checkout">
                                            <button type="submit" class="btn btn-sm btn-danger ms-1">Check Out</button>
                                        </form>
                                        <form action="{{ route('gym.members.destroy', 2) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Delete this member? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card shadow">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <span class="me-3">Bulk Actions:</span>
                        <select class="form-select me-2" style="width: auto;">
                            <option value="">Choose Action</option>
                            <option value="activate">Activate Selected</option>
                            <option value="suspend">Suspend Selected</option>
                            <option value="delete">Delete Selected</option>
                            <option value="export">Export Selected</option>
                        </select>
                        <button class="btn btn-primary" id="applyBulkAction">Apply</button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-outline-primary me-2">
                        <i class="fas fa-download me-1"></i>
                        Export CSV
                    </button>
                    <button class="btn btn-outline-danger">
                        <i class="fas fa-file-pdf me-1"></i>
                        Export PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.avatar img {
    object-fit: cover;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    color: #5a5c69;
    background-color: #f8f9fc;
}

.badge {
    font-size: 0.75rem;
}

.dropdown-toggle::after {
    margin-left: 0.5rem;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .d-sm-flex {
        flex-direction: column;
        align-items: stretch !important;
    }
    
    .d-sm-flex .btn {
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const memberCheckboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    
    selectAllCheckbox.addEventListener('change', function() {
        memberCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Bulk actions
    document.getElementById('applyBulkAction')?.addEventListener('click', function() {
        const selectedMembers = Array.from(memberCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
            
        if (selectedMembers.length === 0) {
            alert('Please select at least one member.');
            return;
        }
        
        const action = document.querySelector('select[name="bulk_action"]')?.value;
        if (!action) {
            alert('Please select an action.');
            return;
        }
        
        if (confirm(`Are you sure you want to ${action} ${selectedMembers.length} member(s)?`)) {
            // Implement bulk action logic here
            console.log('Bulk action:', action, 'Members:', selectedMembers);
        }
    });
});
</script>
@endpush
@endsection
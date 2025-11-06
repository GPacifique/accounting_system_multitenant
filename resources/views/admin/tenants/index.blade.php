{{-- resources/views/admin/tenants/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Tenant Management - System Administration | SiteLedger')
@section('meta_description', 'Comprehensive tenant management interface for SiteLedger multitenant accounting system. View, create, edit and monitor all business tenants.')
@section('meta_keywords', 'tenant management, multitenant administration, business management, tenant monitoring, system administration')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold flex items-center">
                    <div class="theme-aware-bg-card/20 rounded-lg p-2 mr-4">
                        <i class="fas fa-building text-2xl"></i>
                    </div>
                    Tenant Management
                </h1>
                <p class="text-blue-100 mt-2">Manage all business tenants in the system</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.tenants.create') }}" class="theme-aware-bg-card text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
                    <i class="fas fa-plus mr-2"></i>
                    New Tenant
                </a>
                <button onclick="exportTenants()" class="bg-blue-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-400 transition">
                    <i class="fas fa-download mr-2"></i>
                    Export
                </button>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium theme-aware-text-secondary">Active Tenants</p>
                    <p class="text-2xl font-bold text-green-600">{{ $tenants->where('status', 'active')->count() }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium theme-aware-text-secondary">Inactive Tenants</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $tenants->where('status', 'inactive')->count() }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <i class="fas fa-pause-circle text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium theme-aware-text-secondary">Total Users</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $tenants->sum('users_count') }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium theme-aware-text-secondary">New This Month</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $tenants->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-calendar-plus text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters and Search --}}
    <div class="theme-aware-bg-card rounded-xl shadow-lg p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col md:flex-row gap-4 flex-grow">
                <div class="flex-grow">
                    <div class="relative">
                        <input type="text" 
                               id="searchTenants" 
                               placeholder="Search tenants by name, domain, or business type..."
                               class="w-full pl-10 pr-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search theme-aware-text-muted"></i>
                        </div>
                    </div>
                </div>
                
                <select id="statusFilter" class="px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                </select>

                <select id="businessTypeFilter" class="px-4 py-3 border theme-aware-border rounded-lg focus:ring-2 focus:ring-primary focus:theme-aware-border-focus">
                    <option value="">All Business Types</option>
                    <option value="construction">Construction</option>
                    <option value="consulting">Consulting</option>
                    <option value="retail">Retail</option>
                    <option value="manufacturing">Manufacturing</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button onclick="clearFilters()" class="px-4 py-3 theme-aware-bg-secondary theme-aware-text-secondary rounded-lg hover:theme-aware-bg-tertiary transition">
                    <i class="fas fa-times mr-2"></i>
                    Clear
                </button>
                <button onclick="applyFilters()" class="px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-filter mr-2"></i>
                    Filter
                </button>
            </div>
        </div>
    </div>

    {{-- Tenants Table --}}
    <div class="theme-aware-bg-card rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="theme-aware-bg-secondary">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded theme-aware-border text-blue-600 focus:ring-primary">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider cursor-pointer hover:theme-aware-bg-secondary" onclick="sortBy('name')">
                            Tenant Details
                            <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider cursor-pointer hover:theme-aware-bg-secondary" onclick="sortBy('business_type')">
                            Business Type
                            <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider cursor-pointer hover:theme-aware-bg-secondary" onclick="sortBy('users_count')">
                            Users
                            <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider cursor-pointer hover:theme-aware-bg-secondary" onclick="sortBy('subscription_plan')">
                            Plan
                            <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider cursor-pointer hover:theme-aware-bg-secondary" onclick="sortBy('status')">
                            Status
                            <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider cursor-pointer hover:theme-aware-bg-secondary" onclick="sortBy('created_at')">
                            Created
                            <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium theme-aware-text-muted uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="theme-aware-bg-card divide-y divide-gray-200" id="tenantsTableBody">
                    @forelse($tenants as $tenant)
                        <tr class="hover:theme-aware-bg-secondary tenant-row" data-tenant-id="{{ $tenant->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="tenant-checkbox rounded theme-aware-border text-blue-600 focus:ring-primary" value="{{ $tenant->id }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-4">
                                        <span class="text-white font-semibold text-sm">{{ strtoupper(substr($tenant->name, 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium theme-aware-text">{{ $tenant->name }}</div>
                                        <div class="text-sm theme-aware-text-muted">{{ $tenant->domain ?? 'No domain set' }}</div>
                                        <div class="text-xs theme-aware-text-muted">{{ $tenant->contact_email ?? 'No email' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm theme-aware-text">{{ ucfirst($tenant->business_type ?? 'Not specified') }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium theme-aware-text">{{ $tenant->users_count ?? 0 }}</span>
                                    <a href="{{ route('admin.tenants.users', $tenant) }}" class="ml-2 text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ ($tenant->subscription_plan ?? 'basic') === 'premium' ? 'bg-purple-100 text-purple-800' : 
                                       (($tenant->subscription_plan ?? 'basic') === 'professional' ? 'bg-blue-100 text-blue-800' : 'theme-aware-bg-secondary theme-aware-text') }}">
                                    {{ ucfirst($tenant->subscription_plan ?? 'Basic') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $tenant->status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($tenant->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm theme-aware-text-muted">
                                <div>{{ $tenant->created_at->format('M d, Y') }}</div>
                                <div class="text-xs theme-aware-text-muted">{{ $tenant->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.tenants.show', $tenant) }}" 
                                       class="text-blue-600 hover:text-blue-900" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.tenants.edit', $tenant) }}" 
                                       class="text-green-600 hover:text-green-900" 
                                       title="Edit Tenant">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="toggleTenantStatus({{ $tenant->id }}, '{{ $tenant->status }}')" 
                                            class="text-orange-600 hover:text-orange-900" 
                                            title="Toggle Status">
                                        <i class="fas fa-{{ $tenant->status === 'active' ? 'pause' : 'play' }}"></i>
                                    </button>
                                    <button onclick="deleteTenant({{ $tenant->id }}, '{{ $tenant->name }}')" 
                                            class="text-red-600 hover:text-red-900" 
                                            title="Delete Tenant">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center theme-aware-text-muted">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-building text-4xl mb-4 text-gray-300"></i>
                                    <h3 class="text-lg font-medium mb-2">No tenants found</h3>
                                    <p class="text-sm mb-4">Get started by creating your first tenant</p>
                                    <a href="{{ route('admin.tenants.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                        <i class="fas fa-plus mr-2"></i>
                                        Create Tenant
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($tenants->hasPages())
            <div class="theme-aware-bg-card px-6 py-4 border-t theme-aware-border">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>

    {{-- Bulk Actions Panel --}}
    <div id="bulkActionsPanel" class="hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-6 py-4 rounded-lg shadow-lg">
        <div class="flex items-center space-x-4">
            <span id="selectedCount">0 selected</span>
            <button onclick="bulkAction('activate')" class="bg-green-600 px-3 py-1 rounded text-sm hover:bg-green-700">
                <i class="fas fa-play mr-1"></i> Activate
            </button>
            <button onclick="bulkAction('deactivate')" class="bg-orange-600 px-3 py-1 rounded text-sm hover:bg-orange-700">
                <i class="fas fa-pause mr-1"></i> Deactivate
            </button>
            <button onclick="bulkAction('delete')" class="bg-red-600 px-3 py-1 rounded text-sm hover:bg-red-700">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
            <button onclick="clearSelection()" class="bg-gray-600 px-3 py-1 rounded text-sm hover:bg-gray-700">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
let selectedTenants = [];
let sortDirection = 'asc';
let currentSortField = 'name';

// Search and filter functionality
document.getElementById('searchTenants').addEventListener('input', filterTenants);
document.getElementById('statusFilter').addEventListener('change', filterTenants);
document.getElementById('businessTypeFilter').addEventListener('change', filterTenants);

function filterTenants() {
    const search = document.getElementById('searchTenants').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const businessType = document.getElementById('businessTypeFilter').value;
    
    const rows = document.querySelectorAll('.tenant-row');
    
    rows.forEach(row => {
        const tenantText = row.textContent.toLowerCase();
        const rowStatus = row.querySelector('.px-2').textContent.toLowerCase().trim();
        const rowBusinessType = row.cells[2].textContent.toLowerCase().trim();
        
        const matchesSearch = !search || tenantText.includes(search);
        const matchesStatus = !status || rowStatus === status;
        const matchesBusinessType = !businessType || rowBusinessType === businessType;
        
        row.style.display = matchesSearch && matchesStatus && matchesBusinessType ? '' : 'none';
    });
}

function clearFilters() {
    document.getElementById('searchTenants').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('businessTypeFilter').value = '';
    filterTenants();
}

function applyFilters() {
    filterTenants();
}

// Sorting functionality
function sortBy(field) {
    if (currentSortField === field) {
        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        sortDirection = 'asc';
        currentSortField = field;
    }
    
    const tbody = document.getElementById('tenantsTableBody');
    const rows = Array.from(tbody.querySelectorAll('.tenant-row'));
    
    rows.sort((a, b) => {
        let aValue, bValue;
        
        switch(field) {
            case 'name':
                aValue = a.cells[1].textContent.trim();
                bValue = b.cells[1].textContent.trim();
                break;
            case 'business_type':
                aValue = a.cells[2].textContent.trim();
                bValue = b.cells[2].textContent.trim();
                break;
            case 'users_count':
                aValue = parseInt(a.cells[3].textContent.trim());
                bValue = parseInt(b.cells[3].textContent.trim());
                break;
            case 'subscription_plan':
                aValue = a.cells[4].textContent.trim();
                bValue = b.cells[4].textContent.trim();
                break;
            case 'status':
                aValue = a.cells[5].textContent.trim();
                bValue = b.cells[5].textContent.trim();
                break;
            case 'created_at':
                aValue = new Date(a.cells[6].textContent.trim());
                bValue = new Date(b.cells[6].textContent.trim());
                break;
            default:
                return 0;
        }
        
        if (typeof aValue === 'string') {
            aValue = aValue.toLowerCase();
            bValue = bValue.toLowerCase();
        }
        
        if (sortDirection === 'asc') {
            return aValue > bValue ? 1 : -1;
        } else {
            return aValue < bValue ? 1 : -1;
        }
    });
    
    // Re-append sorted rows
    rows.forEach(row => tbody.appendChild(row));
}

// Selection functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.tenant-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateSelection();
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('tenant-checkbox')) {
        updateSelection();
    }
});

function updateSelection() {
    const checkboxes = document.querySelectorAll('.tenant-checkbox:checked');
    selectedTenants = Array.from(checkboxes).map(cb => cb.value);
    
    const panel = document.getElementById('bulkActionsPanel');
    const count = document.getElementById('selectedCount');
    
    if (selectedTenants.length > 0) {
        panel.classList.remove('hidden');
        count.textContent = `${selectedTenants.length} selected`;
    } else {
        panel.classList.add('hidden');
    }
}

function clearSelection() {
    document.querySelectorAll('.tenant-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateSelection();
}

// Action functions
function toggleTenantStatus(tenantId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const action = newStatus === 'active' ? 'activate' : 'deactivate';
    
    if (confirm(`${action.charAt(0).toUpperCase() + action.slice(1)} this tenant?`)) {
        // Simulate API call
        console.log(`Toggling tenant ${tenantId} to ${newStatus}`);
        // Replace with actual API call
        location.reload();
    }
}

function deleteTenant(tenantId, tenantName) {
    if (confirm(`Are you sure you want to delete "${tenantName}"? This action cannot be undone.`)) {
        // Simulate API call
        console.log(`Deleting tenant ${tenantId}`);
        // Replace with actual API call
        location.reload();
    }
}

function bulkAction(action) {
    if (selectedTenants.length === 0) return;
    
    const confirmMessage = {
        'activate': `Activate ${selectedTenants.length} tenants?`,
        'deactivate': `Deactivate ${selectedTenants.length} tenants?`,
        'delete': `Delete ${selectedTenants.length} tenants? This action cannot be undone.`
    };
    
    if (confirm(confirmMessage[action])) {
        console.log(`Bulk ${action} for tenants:`, selectedTenants);
        // Replace with actual API call
        location.reload();
    }
}

function exportTenants() {
    const format = prompt('Export format (csv/excel/pdf):', 'csv');
    if (format) {
        console.log(`Exporting tenants as ${format}`);
        // Replace with actual export functionality
        window.open(`/admin/tenants/export?format=${format}`, '_blank');
    }
}
</script>
@endsection
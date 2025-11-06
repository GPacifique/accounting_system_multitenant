<!-- resources/views/layouts/sidebar.blade.php -->
<!-- Enhanced Polished Sidebar with All Features -->
<aside class="sidebar-wrapper">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo/siteledger-sidebar.svg') }}" alt="SiteLedger Logo" class="sidebar-logo">
            <span class="brand-text">SiteLedger</span>
        </div>
        <!-- Theme Toggle Button -->
        <div class="sidebar-theme-toggle">
            @include('components.theme-toggle')
        </div>
    </div>

    {{-- Active Role Indicator --}}
    @auth
        @php
            $activeRole = session('active_role');
            $userRoles = auth()->user()->roles->pluck('name')->toArray();
            $user = auth()->user();
        @endphp
        
        @if($activeRole)
            <div class="alert alert-info mx-3 mt-3 mb-2 py-2 px-3" style="font-size: 0.85rem; border-left: 4px solid #0dcaf0;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-filter me-2"></i>
                        <strong>Active View:</strong>
                        <span class="badge bg-info ms-1">{{ ucfirst($activeRole) }}</span>
                    </div>
                    <form method="POST" action="{{ route('role.clear') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-info py-0 px-2" style="font-size: 0.75rem;" title="Clear filter">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                </div>
            </div>
        @elseif(count($userRoles) > 1)
            <div class="alert alert-secondary mx-3 mt-3 mb-2 py-2 px-3" style="font-size: 0.85rem;">
                <i class="fas fa-users-cog me-2"></i>
                <strong>Viewing:</strong>
                <span class="badge bg-secondary ms-1">All Roles</span>
            </div>
        @endif
    @endauth

    <!-- Main Navigation -->
    <nav class="sidebar-nav">
        @auth
            <!-- Dashboard Section -->
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-tachometer-alt sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Dashboard</span>
                </div>
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line sidebar-icon"></i>
                    <span class="sidebar-text">Overview</span>
                    <span class="sidebar-badge bg-success">Live</span>
                </a>
            </div>

            <!-- Core Features - Available to All -->
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-layers sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Core Features</span>
                </div>

                <!-- Dashboard Analytics -->
                <a href="{{ route('dashboard.analytics') }}" class="sidebar-link {{ request()->routeIs('dashboard.analytics') ? 'active' : '' }}">
                    <i class="fas fa-chart-line sidebar-icon"></i>
                    <span class="sidebar-text">Analytics</span>
                    @if(request()->routeIs('dashboard.analytics'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>

                <!-- Accounts Management -->
                <a href="{{ route('accounts.index') }}" class="sidebar-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-tree-map sidebar-icon"></i>
                    <span class="sidebar-text">Accounts</span>
                    @if(request()->routeIs('accounts.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>

                <!-- Reports -->
                <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar sidebar-icon"></i>
                    <span class="sidebar-text">Reports</span>
                    @if(request()->routeIs('reports.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>

                <!-- Settings -->
                <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog sidebar-icon"></i>
                    <span class="sidebar-text">Settings</span>
                    @if(request()->routeIs('settings.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
            </div>

            <!-- Business Management Section -->
            @if(auth()->user()->can('view customers') || auth()->user()->can('view suppliers') || auth()->user()->can('view products'))
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-building sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Business Management</span>
                </div>

                <!-- Customers -->
                @can('view customers')
                <a href="{{ route('customers.index') }}" class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="fas fa-users sidebar-icon"></i>
                    <span class="sidebar-text">Customers</span>
                    @if(request()->routeIs('customers.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan

                <!-- Suppliers -->
                @can('view suppliers')
                <a href="{{ route('suppliers.index') }}" class="sidebar-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="fas fa-truck sidebar-icon"></i>
                    <span class="sidebar-text">Suppliers</span>
                    @if(request()->routeIs('suppliers.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan

                <!-- Products & Inventory -->
                @can('view products')
                <a href="{{ route('products.index') }}" class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-box sidebar-icon"></i>
                    <span class="sidebar-text">Products</span>
                    @if(request()->routeIs('products.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan

                <!-- Inventory -->
                @can('view inventory')
                <a href="{{ route('inventory.index') }}" class="sidebar-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                    <i class="fas fa-warehouse sidebar-icon"></i>
                    <span class="sidebar-text">Inventory</span>
                    @if(request()->routeIs('inventory.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan
            </div>
            @endif

            <!-- Financial Management Section -->
            @if(auth()->user()->can('view invoices') || auth()->user()->can('view payments') || auth()->user()->can('view expenses'))
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-money-bill-wave sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Financial Management</span>
                </div>

                <!-- Invoices -->
                @can('view invoices')
                <a href="{{ route('invoices.index') }}" class="sidebar-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar sidebar-icon"></i>
                    <span class="sidebar-text">Invoices</span>
                    @if(request()->routeIs('invoices.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan

                <!-- Payments -->
                @can('view payments')
                <a href="{{ route('payments.index') }}" class="sidebar-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card sidebar-icon"></i>
                    <span class="sidebar-text">Payments</span>
                    @if(request()->routeIs('payments.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan

                <!-- Expenses -->
                @can('view expenses')
                <a href="{{ route('expenses.index') }}" class="sidebar-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                    <i class="fas fa-receipt sidebar-icon"></i>
                    <span class="sidebar-text">Expenses</span>
                    @if(request()->routeIs('expenses.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan

                <!-- Budgets -->
                @can('view budgets')
                <a href="{{ route('budgets.index') }}" class="sidebar-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
                    <i class="fas fa-calculator sidebar-icon"></i>
                    <span class="sidebar-text">Budgets</span>
                    @if(request()->routeIs('budgets.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan

                <!-- Financial Reports -->
                @can('view financial reports')
                <a href="{{ route('reports.financial') }}" class="sidebar-link {{ request()->routeIs('reports.financial') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie sidebar-icon"></i>
                    <span class="sidebar-text">Financial Reports</span>
                    @if(request()->routeIs('reports.financial'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan
            </div>
            @endif

            <!-- Project Management Section -->
            @if(auth()->user()->can('view projects') || auth()->user()->can('view tasks') || auth()->user()->can('view time tracking'))
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-project-diagram sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Project Management</span>
                </div>

                <!-- Projects -->
                @can('view projects')
                <a href="{{ route('projects.index') }}" class="sidebar-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                    <i class="fas fa-tasks sidebar-icon"></i>
                    <span class="sidebar-text">Projects</span>
                    @if(request()->routeIs('projects.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan

                <!-- Tasks -->
                @can('view tasks')
                <a href="{{ route('tasks.index') }}" class="sidebar-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                    <i class="fas fa-check-square sidebar-icon"></i>
                    <span class="sidebar-text">Tasks</span>
                    @if(request()->routeIs('tasks.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan

                <!-- Time Tracking -->
                @can('view time tracking')
                <a href="{{ route('time-tracking.index') }}" class="sidebar-link {{ request()->routeIs('time-tracking.*') ? 'active' : '' }}">
                    <i class="fas fa-clock sidebar-icon"></i>
                    <span class="sidebar-text">Time Tracking</span>
                    @if(request()->routeIs('time-tracking.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan

                <!-- Team Collaboration -->
                @can('view teams')
                <a href="{{ route('teams.index') }}" class="sidebar-link {{ request()->routeIs('teams.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog sidebar-icon"></i>
                    <span class="sidebar-text">Teams</span>
                    @if(request()->routeIs('teams.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan
            </div>
            @endif

            <!-- Human Resources Section -->
            @if(auth()->user()->can('view employees') || auth()->user()->can('view payroll') || auth()->user()->can('view leaves'))
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-user-tie sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Human Resources</span>
                </div>

                <!-- Employees -->
                @can('view employees')
                <a href="{{ route('employees.index') }}" class="sidebar-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                    <i class="fas fa-id-badge sidebar-icon"></i>
                    <span class="sidebar-text">Employees</span>
                    @if(request()->routeIs('employees.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan

                <!-- Payroll -->
                @can('view payroll')
                <a href="{{ route('payroll.index') }}" class="sidebar-link {{ request()->routeIs('payroll.*') ? 'active' : '' }}">
                    <i class="fas fa-money-check-alt sidebar-icon"></i>
                    <span class="sidebar-text">Payroll</span>
                    @if(request()->routeIs('payroll.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan

                <!-- Leave Management -->
                @can('view leaves')
                <a href="{{ route('leaves.index') }}" class="sidebar-link {{ request()->routeIs('leaves.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-times sidebar-icon"></i>
                    <span class="sidebar-text">Leave Management</span>
                    @if(request()->routeIs('leaves.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan

                <!-- Attendance -->
                @can('view attendance')
                <a href="{{ route('attendance.index') }}" class="sidebar-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                    <i class="fas fa-user-clock sidebar-icon"></i>
                    <span class="sidebar-text">Attendance</span>
                    @if(request()->routeIs('attendance.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
                @endcan
            </div>
            @endif
                    <span class="sidebar-text">Products</span>
                    @if(request()->routeIs('products.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>

                <!-- Tasks -->
                <a href="{{ route('tasks.index') }}" class="sidebar-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                    <i class="fas fa-tasks sidebar-icon"></i>
                    <span class="sidebar-text">Tasks</span>
                    @php 
                        $activeTasks = Schema::hasTable('tasks') ? DB::table('tasks')->whereIn('status', ['pending', 'in_progress'])->count() : 0; 
                    @endphp
                    @if($activeTasks > 0)
                        <span class="sidebar-badge bg-primary">{{ $activeTasks }}</span>
                    @endif
                    @if(request()->routeIs('tasks.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>
            </div>

            <!-- Project Management - Manager & Admin -->
            @if($user->hasAnyRole(['admin', 'manager']))
                <div class="sidebar-section">
                    <div class="sidebar-section-header">
                        <i class="fas fa-project-diagram sidebar-section-icon"></i>
                        <span class="sidebar-section-title">Project Management</span>
                    </div>

                    <!-- Projects -->
                    <a href="{{ route('projects.index') }}" class="sidebar-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                        <i class="fas fa-building sidebar-icon"></i>
                        <span class="sidebar-text">Projects</span>
                        <span class="sidebar-badge bg-warning">{{ App\Models\Project::count() ?? 0 }}</span>
                        @if(request()->routeIs('projects.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>

                    <!-- Workers -->
                    <a href="{{ route('workers.index') }}" class="sidebar-link {{ request()->routeIs('workers.*') ? 'active' : '' }}">
                        <i class="fas fa-hard-hat sidebar-icon"></i>
                        <span class="sidebar-text">Workers</span>
                        <span class="sidebar-badge bg-info">{{ App\Models\Worker::count() ?? 0 }}</span>
                        @if(request()->routeIs('workers.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>

                    <!-- Employees -->
                    <a href="{{ route('employees.index') }}" class="sidebar-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <i class="fas fa-users sidebar-icon"></i>
                        <span class="sidebar-text">Employees</span>
                        <span class="sidebar-badge bg-primary">{{ App\Models\Employee::count() ?? 0 }}</span>
                        @if(request()->routeIs('employees.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>

                    <!-- Orders -->
                    <a href="{{ route('orders.index') }}" class="sidebar-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart sidebar-icon"></i>
                        <span class="sidebar-text">Orders</span>
                        @php $pendingOrders = Schema::hasTable('orders') ? DB::table('orders')->where('status', 'pending')->count() : 0; @endphp
                        @if($pendingOrders > 0)
                            <span class="sidebar-badge bg-danger">{{ $pendingOrders }}</span>
                        @endif
                        @if(request()->routeIs('orders.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                </div>
            @endif

            <!-- Financial Management - Accountant & Admin -->
            @if($user->hasAnyRole(['admin', 'accountant']))
                <div class="sidebar-section">
                    <div class="sidebar-section-header">
                        <i class="fas fa-coins sidebar-section-icon"></i>
                        <span class="sidebar-section-title">Financial Management</span>
                    </div>

                    <!-- Incomes -->
                    <a href="{{ route('incomes.index') }}" class="sidebar-link {{ request()->routeIs('incomes.*') ? 'active' : '' }}">
                        <i class="fas fa-arrow-up sidebar-icon text-success"></i>
                        <span class="sidebar-text">Incomes</span>
                        @php $monthlyIncomes = Schema::hasTable('incomes') ? DB::table('incomes')->whereBetween('received_at', [now()->startOfMonth(), now()])->count() : 0; @endphp
                        @if($monthlyIncomes > 0)
                            <span class="sidebar-badge bg-success">{{ $monthlyIncomes }}</span>
                        @endif
                        @if(request()->routeIs('incomes.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>

                    <!-- Expenses -->
                    <a href="{{ route('expenses.index') }}" class="sidebar-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                        <i class="fas fa-arrow-down sidebar-icon text-danger"></i>
                        <span class="sidebar-text">Expenses</span>
                        @php $monthlyExpenses = Schema::hasTable('expenses') ? DB::table('expenses')->whereBetween('created_at', [now()->startOfMonth(), now()])->count() : 0; @endphp
                        @if($monthlyExpenses > 0)
                            <span class="sidebar-badge bg-danger">{{ $monthlyExpenses }}</span>
                        @endif
                        @if(request()->routeIs('expenses.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>

                    <!-- Payments -->
                    <a href="{{ route('payments.index') }}" class="sidebar-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                        <i class="fas fa-credit-card sidebar-icon"></i>
                        <span class="sidebar-text">Payments</span>
                        @php $todayPayments = Schema::hasTable('payments') ? DB::table('payments')->whereDate('created_at', today())->count() : 0; @endphp
                        @if($todayPayments > 0)
                            <span class="sidebar-badge bg-primary">{{ $todayPayments }}</span>
                        @endif
                        @if(request()->routeIs('payments.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>

                    <!-- Finance Overview -->
                    <a href="{{ route('finance.index') }}" class="sidebar-link {{ request()->routeIs('finance.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie sidebar-icon"></i>
                        <span class="sidebar-text">Finance Overview</span>
                        @if(request()->routeIs('finance.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-bolt sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Quick Actions</span>
                </div>

                <div class="sidebar-quick-actions">
                    @if($user->hasAnyRole(['admin', 'accountant']))
                        <a href="{{ route('incomes.create') }}" class="sidebar-quick-btn btn-success" title="Add New Income">
                            <i class="fas fa-plus"></i>
                            <span>Income</span>
                        </a>
                        <a href="{{ route('expenses.create') }}" class="sidebar-quick-btn btn-danger" title="Add New Expense">
                            <i class="fas fa-minus"></i>
                            <span>Expense</span>
                        </a>
                        <a href="{{ route('payments.create') }}" class="sidebar-quick-btn btn-primary" title="Add New Payment">
                            <i class="fas fa-credit-card"></i>
                            <span>Payment</span>
                        </a>
                    @endif

                    @if($user->hasAnyRole(['admin', 'manager']))
                        <a href="{{ route('projects.create') }}" class="sidebar-quick-btn btn-warning" title="Create New Project">
                            <i class="fas fa-building"></i>
                            <span>Project</span>
                        </a>
                        <a href="{{ route('workers.create') }}" class="sidebar-quick-btn btn-info" title="Add New Worker">
                            <i class="fas fa-hard-hat"></i>
                            <span>Worker</span>
                        </a>
                    @endif

                    <a href="{{ route('reports.create') }}" class="sidebar-quick-btn btn-secondary" title="Generate Report">
                        <i class="fas fa-chart-bar"></i>
                        <span>Report</span>
                    </a>
                </div>
            </div>

            <!-- Super Admin - Complete System Control -->
            @if($user->hasRole('super-admin'))
                <!-- Multi-Tenant Management -->
                <div class="sidebar-section">
                    <div class="sidebar-section-header">
                        <i class="fas fa-crown sidebar-section-icon text-warning"></i>
                        <span class="sidebar-section-title">Multi-Tenant System</span>
                        <span class="sidebar-badge bg-warning">SA</span>
                    </div>

                    <!-- Tenant Management -->
                    <a href="{{ route('admin.tenants.index') }}" class="sidebar-link {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}">
                        <i class="fas fa-building sidebar-icon"></i>
                        <span class="sidebar-text">Tenants</span>
                        @php
                            try {
                                $activeTenants = \App\Models\Tenant::where('status', 'active')->count();
                                $totalTenants = \App\Models\Tenant::count();
                            } catch (\Exception $e) {
                                $activeTenants = 0;
                                $totalTenants = 0;
                            }
                        @endphp
                        <span class="sidebar-badge bg-success">{{ $activeTenants }}/{{ $totalTenants }}</span>
                        @if(request()->routeIs('admin.tenants.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>

                    <!-- Tenant Analytics -->
                    <a href="{{ route('admin.analytics') }}" class="sidebar-link {{ request()->routeIs('admin.analytics*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line sidebar-icon"></i>
                        <span class="sidebar-text">System Analytics</span>
                        @if(request()->routeIs('admin.analytics*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>

                    <!-- Tenant Invitations -->
                    @if(Route::has('admin.invitations.index'))
                        <a href="{{ route('admin.invitations.index') }}" class="sidebar-link {{ request()->routeIs('admin.invitations.*') ? 'active' : '' }}">
                            <i class="fas fa-envelope-open sidebar-icon"></i>
                            <span class="sidebar-text">Invitations</span>
                            @php
                                try {
                                    $pendingInvitations = \App\Models\TenantInvitation::where('status', 'pending')->count();
                                } catch (\Exception $e) {
                                    $pendingInvitations = 0;
                                }
                            @endphp
                            @if($pendingInvitations > 0)
                                <span class="sidebar-badge bg-warning">{{ $pendingInvitations }}</span>
                            @endif
                            @if(request()->routeIs('admin.invitations.*'))
                                <span class="sidebar-indicator"></span>
                            @endif
                        </a>
                    @endif

                    <!-- Tenant Subscriptions -->
                    @if(Route::has('admin.subscriptions.index'))
                        <a href="{{ route('admin.subscriptions.index') }}" class="sidebar-link {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                            <i class="fas fa-credit-card sidebar-icon"></i>
                            <span class="sidebar-text">Subscriptions</span>
                            @php
                                try {
                                    $expiringSoon = \App\Models\TenantSubscription::where('next_billing_date', '<=', now()->addDays(7))->count();
                                } catch (\Exception $e) {
                                    $expiringSoon = 0;
                                }
                            @endphp
                            @if($expiringSoon > 0)
                                <span class="sidebar-badge bg-danger">{{ $expiringSoon }}</span>
                            @endif
                            @if(request()->routeIs('admin.subscriptions.*'))
                                <span class="sidebar-indicator"></span>
                            @endif
                        </a>
                    @endif

                    <!-- Audit Logs -->
                    @if(Route::has('admin.audit-logs.index'))
                        <a href="{{ route('admin.audit-logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-list sidebar-icon"></i>
                            <span class="sidebar-text">Audit Logs</span>
                            @if(request()->routeIs('admin.audit-logs.*'))
                                <span class="sidebar-indicator"></span>
                            @endif
                        </a>
                    @endif
                </div>

                <!-- System Administration -->
                <div class="sidebar-section">
                    <div class="sidebar-section-header">
                        <i class="fas fa-cogs sidebar-section-icon"></i>
                        <span class="sidebar-section-title">System Administration</span>
                    </div>

                    <!-- User Management -->
                    <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog sidebar-icon"></i>
                        <span class="sidebar-text">User Management</span>
                        <span class="sidebar-badge bg-primary">{{ App\Models\User::count() ?? 0 }}</span>
                        @if(request()->routeIs('users.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>

                    <!-- Role Management -->
                    <a href="{{ route('roles.index') }}" class="sidebar-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                        <i class="fas fa-user-shield sidebar-icon"></i>
                        <span class="sidebar-text">Roles & Access</span>
                        <span class="sidebar-badge bg-info">{{ \Spatie\Permission\Models\Role::count() ?? 0 }}</span>
                        @if(request()->routeIs('roles.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>

                    <!-- Permission Management -->
                    <a href="{{ route('permissions.index') }}" class="sidebar-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                        <i class="fas fa-shield-alt sidebar-icon"></i>
                        <span class="sidebar-text">Permissions</span>
                        <span class="sidebar-badge bg-secondary">{{ \Spatie\Permission\Models\Permission::count() ?? 0 }}</span>
                        @if(request()->routeIs('permissions.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>

                    <!-- System Settings -->
                    @if(Route::has('admin.settings.index'))
                        <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <i class="fas fa-sliders-h sidebar-icon"></i>
                            <span class="sidebar-text">System Settings</span>
                            @if(request()->routeIs('admin.settings.*'))
                                <span class="sidebar-indicator"></span>
                            @endif
                        </a>
                    @endif

                    <!-- System Logs -->
                    @if(Route::has('admin.logs.index'))
                        <a href="{{ route('admin.logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                            <i class="fas fa-file-alt sidebar-icon"></i>
                            <span class="sidebar-text">System Logs</span>
                            @if(request()->routeIs('admin.logs.*'))
                                <span class="sidebar-indicator"></span>
                            @endif
                        </a>
                    @endif
                </div>

                <!-- Data Management -->
                <div class="sidebar-section">
                    <div class="sidebar-section-header">
                        <i class="fas fa-database sidebar-section-icon"></i>
                        <span class="sidebar-section-title">Data Management</span>
                    </div>

                    <!-- Data Import/Export -->
                    @if(Route::has('admin.data.index'))
                        <a href="{{ route('admin.data.index') }}" class="sidebar-link {{ request()->routeIs('admin.data.*') ? 'active' : '' }}">
                            <i class="fas fa-exchange-alt sidebar-icon"></i>
                            <span class="sidebar-text">Import/Export</span>
                            @if(request()->routeIs('admin.data.*'))
                                <span class="sidebar-indicator"></span>
                            @endif
                        </a>
                    @endif

                    <!-- Backup Management -->
                    @if(Route::has('admin.backups.index'))
                        <a href="{{ route('admin.backups.index') }}" class="sidebar-link {{ request()->routeIs('admin.backups.*') ? 'active' : '' }}">
                            <i class="fas fa-hdd sidebar-icon"></i>
                            <span class="sidebar-text">Backups</span>
                            @php
                                try {
                                    $recentBackups = \App\Models\Tenant::whereNotNull('last_backup_at')->where('last_backup_at', '>=', now()->subDay())->count();
                                } catch (\Exception $e) {
                                    $recentBackups = 0;
                                }
                            @endphp
                            @if($recentBackups > 0)
                                <span class="sidebar-badge bg-success">{{ $recentBackups }}</span>
                            @endif
                            @if(request()->routeIs('admin.backups.*'))
                                <span class="sidebar-indicator"></span>
                            @endif
                        </a>
                    @endif

                    <!-- Database Management -->
                    @if(Route::has('admin.database.index'))
                        <a href="{{ route('admin.database.index') }}" class="sidebar-link {{ request()->routeIs('admin.database.*') ? 'active' : '' }}">
                            <i class="fas fa-database sidebar-icon"></i>
                            <span class="sidebar-text">Database</span>
                            @if(request()->routeIs('admin.database.*'))
                                <span class="sidebar-indicator"></span>
                            @endif
                        </a>
                    @endif
                </div>

                <!-- Advanced Features -->
                <div class="sidebar-section">
                    <div class="sidebar-section-header">
                        <i class="fas fa-rocket sidebar-section-icon"></i>
                        <span class="sidebar-section-title">Advanced Features</span>
                    </div>

                    <!-- API Management -->
                    @if(Route::has('admin.api.index'))
                        <a href="{{ route('admin.api.index') }}" class="sidebar-link {{ request()->routeIs('admin.api.*') ? 'active' : '' }}">
                            <i class="fas fa-code sidebar-icon"></i>
                            <span class="sidebar-text">API Management</span>
                            @if(request()->routeIs('admin.api.*'))
                                <span class="sidebar-indicator"></span>
                            @endif
                        </a>
                    @endif

                    <!-- Webhooks -->
                    @if(Route::has('admin.webhooks.index'))
                        <a href="{{ route('admin.webhooks.index') }}" class="sidebar-link {{ request()->routeIs('admin.webhooks.*') ? 'active' : '' }}">
                            <i class="fas fa-link sidebar-icon"></i>
                            <span class="sidebar-text">Webhooks</span>
                            @if(request()->routeIs('admin.webhooks.*'))
                                <span class="sidebar-indicator"></span>
                            @endif
                        </a>
                    @endif

                    <!-- Integrations -->
                    @if(Route::has('admin.integrations.index'))
                        <a href="{{ route('admin.integrations.index') }}" class="sidebar-link {{ request()->routeIs('admin.integrations.*') ? 'active' : '' }}">
                            <i class="fas fa-plug sidebar-icon"></i>
                            <span class="sidebar-text">Integrations</span>
                            @if(request()->routeIs('admin.integrations.*'))
                                <span class="sidebar-indicator"></span>
                            @endif
                        </a>
                    @endif

                    <!-- Custom Fields -->
                    @if(Route::has('admin.custom-fields.index'))
                        <a href="{{ route('admin.custom-fields.index') }}" class="sidebar-link {{ request()->routeIs('admin.custom-fields.*') ? 'active' : '' }}">
                            <i class="fas fa-plus-square sidebar-icon"></i>
                            <span class="sidebar-text">Custom Fields</span>
                            @if(request()->routeIs('admin.custom-fields.*'))
                                <span class="sidebar-indicator"></span>
                            @endif
                        </a>
                    @endif

                    <!-- Notifications -->
                    @if(Route::has('admin.notifications.index'))
                        <a href="{{ route('admin.notifications.index') }}" class="sidebar-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                            <i class="fas fa-bell sidebar-icon"></i>
                            <span class="sidebar-text">Notifications</span>
                            @php
                                try {
                                    $unreadNotifications = auth()->user()->unreadNotifications->count();
                                } catch (\Exception $e) {
                                    $unreadNotifications = 0;
                                }
                            @endphp
                            @if($unreadNotifications > 0)
                                <span class="sidebar-badge bg-danger">{{ $unreadNotifications }}</span>
                            @endif
                            @if(request()->routeIs('admin.notifications.*'))
                                <span class="sidebar-indicator"></span>
                            @endif
                        </a>
                    @endif
                </div>

                <!-- Super Admin Quick Actions -->
                <div class="sidebar-section">
                    <div class="sidebar-section-header">
                        <i class="fas fa-magic sidebar-section-icon"></i>
                        <span class="sidebar-section-title">Super Admin Actions</span>
                    </div>

                    <div class="sidebar-quick-actions">
                        @if(Route::has('admin.tenants.create'))
                            <a href="{{ route('admin.tenants.create') }}" class="sidebar-quick-btn btn-success" title="Create New Tenant">
                                <i class="fas fa-building"></i>
                                <span>Tenant</span>
                            </a>
                        @endif
                        @if(Route::has('users.create'))
                            <a href="{{ route('users.create') }}" class="sidebar-quick-btn btn-primary" title="Create New User">
                                <i class="fas fa-user-plus"></i>
                                <span>User</span>
                            </a>
                        @endif
                        @if(Route::has('admin.backups.create'))
                            <a href="{{ route('admin.backups.create') }}" class="sidebar-quick-btn btn-warning" title="Create System Backup">
                                <i class="fas fa-download"></i>
                                <span>Backup</span>
                            </a>
                        @endif
                        @if(Route::has('admin.analytics'))
                            <a href="{{ route('admin.analytics') }}" class="sidebar-quick-btn btn-info" title="View System Analytics">
                                <i class="fas fa-chart-bar"></i>
                                <span>Analytics</span>
                            </a>
                        @endif
                        @if(Route::has('admin.data.export'))
                            <a href="{{ route('admin.data.export', ['type' => 'all']) }}" class="sidebar-quick-btn btn-secondary" title="Export System Data">
                                <i class="fas fa-file-export"></i>
                                <span>Export</span>
                            </a>
                        @endif
                        @if(Route::has('admin.notifications.send'))
                            <a href="{{ route('admin.notifications.send') }}" class="sidebar-quick-btn btn-danger" title="Send System Notification">
                                <i class="fas fa-broadcast-tower"></i>
                                <span>Notify</span>
                            </a>
                        @endif
                    </div>
                </div>

            @elseif($user->hasRole('admin'))
                <!-- Standard Administration for Admins -->
                <div class="sidebar-section">
                    <div class="sidebar-section-header">
                        <i class="fas fa-cogs sidebar-section-icon"></i>
                        <span class="sidebar-section-title">Administration</span>
                    </div>

                    <!-- User Management -->
                    <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fas fa-user-cog sidebar-icon"></i>
                        <span class="sidebar-text">Users</span>
                        <span class="sidebar-badge bg-secondary">{{ App\Models\User::count() ?? 0 }}</span>
                        @if(request()->routeIs('users.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>

                    <!-- Role Management -->
                    <a href="{{ route('roles.index') }}" class="sidebar-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                        <i class="fas fa-user-shield sidebar-icon"></i>
                        <span class="sidebar-text">Roles</span>
                        @if(request()->routeIs('roles.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>

                    <!-- Permission Management -->
                    <a href="{{ route('permissions.index') }}" class="sidebar-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                        <i class="fas fa-lock sidebar-icon"></i>
                        <span class="sidebar-text">Permissions</span>
                        @if(request()->routeIs('permissions.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>

                    <!-- Settings -->
                    <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="fas fa-sliders-h sidebar-icon"></i>
                        <span class="sidebar-text">Settings</span>
                        @if(request()->routeIs('settings.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                </div>
            @endif

            <!-- Profile & Account -->
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-user sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Account</span>
                </div>

                <!-- Profile -->
                <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user-edit sidebar-icon"></i>
                    <span class="sidebar-text">Profile</span>
                    @if(request()->routeIs('profile.*'))
                        <span class="sidebar-indicator"></span>
                    @endif
                </a>

                <!-- Role Switching (if multiple roles) -->
                @if(count($userRoles) > 1)
                    <div class="sidebar-role-switcher">
                        <span class="sidebar-label">Switch Role:</span>
                        @foreach($userRoles as $role)
                            <form method="POST" action="{{ route('role.switch', $role) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="sidebar-role-btn {{ $activeRole === $role ? 'active' : '' }}" title="Switch to {{ ucfirst($role) }}">
                                    {{ ucfirst($role) }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                @endif
            </div>

        @endauth
    </nav>

    <!-- Sidebar Footer with Enhanced User Info -->
    <div class="sidebar-footer">
        @auth
            <div class="user-info">
                <div class="user-avatar">
                    <div class="avatar-circle">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                    </div>
                </div>
                <div class="user-details">
                    <div class="user-name">{{ $user->name ?? 'User' }}</div>
                    <div class="user-email">{{ $user->email ?? '' }}</div>
                    @if($user->roles->count() > 0)
                        <div class="user-role">
                            @foreach($user->roles->take(1) as $role)
                                <span class="role-badge">{{ ucfirst($role->name) }}</span>
                            @endforeach
                            @if($user->is_super_admin)
                                <span class="role-badge super-admin">SA</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            <div class="sidebar-footer-actions">
                <!-- Theme Toggle -->
                <button type="button" class="footer-action-btn theme-toggle-mini" title="Toggle Theme">
                    <i class="fas fa-moon"></i>
                </button>
                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="footer-action-btn logout-btn" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        @endauth
    </div>
</aside>

<!-- Mobile Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Mobile Sidebar Toggle Button -->
<button class="mobile-sidebar-toggle" id="mobileSidebarToggle">
    <span class="hamburger-line"></span>
    <span class="hamburger-line"></span>
    <span class="hamburger-line"></span>
</button>

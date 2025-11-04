<!-- resources/views/layouts/sidebar.blade.php -->
<!-- Authentication-Aware Dynamic Sidebar -->
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
            <!-- Dashboard - Available to all authenticated users -->
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-line sidebar-icon"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>

            <!-- Reports - Available to everyone -->
            <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt sidebar-icon"></i>
                <span class="sidebar-text">Reports</span>
            </a>

            <!-- Clients - Available to everyone -->
            <a href="{{ route('clients.index') }}" class="sidebar-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i class="fas fa-handshake sidebar-icon"></i>
                <span class="sidebar-text">Clients</span>
            </a>

            <!-- Transactions - Available to everyone -->
            <a href="{{ route('transactions.index') }}" class="sidebar-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt sidebar-icon"></i>
                <span class="sidebar-text">Transactions</span>
            </a>

            <!-- Products - Available to everyone -->
            <a href="{{ route('products.index') }}" class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="fas fa-box sidebar-icon"></i>
                <span class="sidebar-text">Products</span>
            </a>

            <!-- Manager & Admin Section -->
            @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                <div class="sidebar-divider">
                    <span class="sidebar-section-title">Management</span>
                </div>

                <!-- Projects -->
                <a href="{{ route('projects.index') }}" class="sidebar-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                    <i class="fas fa-project-diagram sidebar-icon"></i>
                    <span class="sidebar-text">Projects</span>
                </a>

                <!-- Workers -->
                <a href="{{ route('workers.index') }}" class="sidebar-link {{ request()->routeIs('workers.*') ? 'active' : '' }}">
                    <i class="fas fa-hard-hat sidebar-icon"></i>
                    <span class="sidebar-text">Workers</span>
                </a>

                <!-- Orders -->
                <a href="{{ route('orders.index') }}" class="sidebar-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart sidebar-icon"></i>
                    <span class="sidebar-text">Orders</span>
                </a>
            @endif

            <!-- Accountant & Admin Section -->
            @if(auth()->user()->hasAnyRole(['admin', 'accountant']))
                <div class="sidebar-divider">
                    <span class="sidebar-section-title">Finance</span>
                </div>

                <!-- Expenses -->
                <a href="{{ route('expenses.index') }}" class="sidebar-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave sidebar-icon"></i>
                    <span class="sidebar-text">Expenses</span>
                </a>

                <!-- Incomes -->
                <a href="{{ route('incomes.index') }}" class="sidebar-link {{ request()->routeIs('incomes.*') ? 'active' : '' }}">
                    <i class="fas fa-coins sidebar-icon"></i>
                    <span class="sidebar-text">Incomes</span>
                </a>

                <!-- Payments -->
                <a href="{{ route('payments.index') }}" class="sidebar-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card sidebar-icon"></i>
                    <span class="sidebar-text">Payments</span>
                </a>
            @endif

            <!-- Admin Only Section -->
            @if(auth()->user()->hasRole('admin'))
                <div class="sidebar-divider">
                    <span class="sidebar-section-title">Administration</span>
                </div>

                <!-- User Management -->
                <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog sidebar-icon"></i>
                    <span class="sidebar-text">Users</span>
                </a>

                <!-- Role Management -->
                <a href="{{ route('roles.index') }}" class="sidebar-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <i class="fas fa-user-shield sidebar-icon"></i>
                    <span class="sidebar-text">Roles</span>
                </a>

                <!-- Permission Management -->
                <a href="{{ route('permissions.index') }}" class="sidebar-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                    <i class="fas fa-lock sidebar-icon"></i>
                    <span class="sidebar-text">Permissions</span>
                </a>

                <!-- Settings -->
                <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                    <i class="fas fa-cog sidebar-icon"></i>
                    <span class="sidebar-text">Settings</span>
                </a>
            @endif

        @endauth
    </nav>

    <!-- Sidebar Footer with User Info -->
    <div class="sidebar-footer">
        @auth
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name ?? 'User' }}</div>
                <div class="user-email">{{ auth()->user()->email ?? '' }}</div>
                @if(auth()->user()->roles->count() > 0)
                    <div class="user-role">
                        @foreach(auth()->user()->roles->take(1) as $role)
                            <span class="role-badge">{{ ucfirst($role->name) }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        @endauth
    </div>
</aside>

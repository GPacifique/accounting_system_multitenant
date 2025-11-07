<!-- resources/views/layouts/gym-sidebar.blade.php -->
<!-- Gym Management Sidebar -->
<aside class="sidebar-wrapper">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="sidebar-brand">
            @if(Route::has('gym.dashboard'))
                <a href="{{ route('gym.dashboard') }}" class="d-flex align-items-center sidebar-brand-link" aria-label="Go to Gym Dashboard">
                    <img src="{{ asset('images/logo/gym-logo.svg') }}" alt="Gym Monitor" class="sidebar-logo">
                    <span class="brand-text">Gym Monitor</span>
                </a>
            @else
                <div class="d-flex align-items-center sidebar-brand-link">
                    <img src="{{ asset('images/logo/gym-logo.svg') }}" alt="Gym Monitor" class="sidebar-logo">
                    <span class="brand-text">Gym Monitor</span>
                </div>
            @endif
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
                @if(Route::has('gym.dashboard'))
                    <a href="{{ route('gym.dashboard') }}" class="sidebar-link {{ request()->routeIs('gym.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line sidebar-icon"></i>
                        <span class="sidebar-text">Gym Overview</span>
                        <span class="sidebar-badge bg-success">Live</span>
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-chart-line sidebar-icon"></i>
                        <span class="sidebar-text">Gym Overview</span>
                    </a>
                @endif

                @if(Route::has('gym.analytics'))
                    <a href="{{ route('gym.analytics') }}" class="sidebar-link {{ request()->routeIs('gym.analytics') ? 'active' : '' }}">
                        <i class="fas fa-analytics sidebar-icon"></i>
                        <span class="sidebar-text">Analytics</span>
                        @if(request()->routeIs('gym.analytics'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-analytics sidebar-icon"></i>
                        <span class="sidebar-text">Analytics</span>
                    </a>
                @endif
            </div>

            <!-- Members Management -->
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-users sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Members</span>
                </div>
                @if(Route::has('gym.members.index'))
                    <a href="{{ route('gym.members.index') }}" class="sidebar-link {{ request()->routeIs('gym.members.*') ? 'active' : '' }}">
                        <i class="fas fa-user sidebar-icon"></i>
                        <span class="sidebar-text">All Members</span>
                        @if(request()->routeIs('gym.members.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-user sidebar-icon"></i>
                        <span class="sidebar-text">All Members</span>
                    </a>
                @endif

                @if(Route::has('gym.members.create'))
                    <a href="{{ route('gym.members.create') }}" class="sidebar-link">
                        <i class="fas fa-user-plus sidebar-icon"></i>
                        <span class="sidebar-text">Add Member</span>
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-user-plus sidebar-icon"></i>
                        <span class="sidebar-text">Add Member</span>
                    </a>
                @endif

                <!-- Membership Plans -->
                @if(Route::has('gym.membership-plans.index'))
                    <a href="{{ route('gym.membership-plans.index') }}" class="sidebar-link {{ request()->routeIs('gym.membership-plans.*') ? 'active' : '' }}">
                        <i class="fas fa-id-card sidebar-icon"></i>
                        <span class="sidebar-text">Membership Plans</span>
                        <span class="sidebar-badge bg-primary">Plans</span>
                        @if(request()->routeIs('gym.membership-plans.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-id-card sidebar-icon"></i>
                        <span class="sidebar-text">Membership Plans</span>
                    </a>
                @endif
                
                <!-- Attendances -->
                @if(Route::has('gym.attendances.index'))
                    <a href="{{ route('gym.attendances.index') }}" class="sidebar-link {{ request()->routeIs('gym.attendances.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check sidebar-icon"></i>
                        <span class="sidebar-text">Attendances</span>
                        @if(request()->routeIs('gym.attendances.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-calendar-check sidebar-icon"></i>
                        <span class="sidebar-text">Attendances</span>
                    </a>
                @endif
            </div>

            <!-- Classes & Training -->
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-dumbbell sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Classes & Training</span>
                </div>
                @if(Route::has('gym.fitness-classes.index'))
                    <a href="{{ route('gym.fitness-classes.index') }}" class="sidebar-link {{ request()->routeIs('gym.fitness-classes.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt sidebar-icon"></i>
                        <span class="sidebar-text">Class Schedule</span>
                        @if(request()->routeIs('gym.fitness-classes.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-calendar-alt sidebar-icon"></i>
                        <span class="sidebar-text">Class Schedule</span>
                    </a>
                @endif

                @if(Route::has('gym.fitness-classes.create'))
                    <a href="{{ route('gym.fitness-classes.create') }}" class="sidebar-link">
                        <i class="fas fa-plus-circle sidebar-icon"></i>
                        <span class="sidebar-text">Schedule Class</span>
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-plus-circle sidebar-icon"></i>
                        <span class="sidebar-text">Schedule Class</span>
                    </a>
                @endif
                <a href="#" class="sidebar-link">
                    <i class="fas fa-bookmark sidebar-icon"></i>
                    <span class="sidebar-text">Class Bookings</span>
                </a>
            </div>

            <!-- Trainers Management -->
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-user-tie sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Trainers</span>
                </div>
                @if(Route::has('gym.trainers.index'))
                    <a href="{{ route('gym.trainers.index') }}" class="sidebar-link {{ request()->routeIs('gym.trainers.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog sidebar-icon"></i>
                        <span class="sidebar-text">All Trainers</span>
                        @if(request()->routeIs('gym.trainers.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-users-cog sidebar-icon"></i>
                        <span class="sidebar-text">All Trainers</span>
                    </a>
                @endif

                @if(Route::has('gym.trainers.create'))
                    <a href="{{ route('gym.trainers.create') }}" class="sidebar-link">
                        <i class="fas fa-user-plus sidebar-icon"></i>
                        <span class="sidebar-text">Add Trainer</span>
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-user-plus sidebar-icon"></i>
                        <span class="sidebar-text">Add Trainer</span>
                    </a>
                @endif
                <a href="#" class="sidebar-link">
                    <i class="fas fa-chart-line sidebar-icon"></i>
                    <span class="sidebar-text">Performance</span>
                </a>
            </div>

            <!-- Equipment Management -->
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-tools sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Equipment</span>
                </div>
                @if(Route::has('gym.equipment.index'))
                    <a href="{{ route('gym.equipment.index') }}" class="sidebar-link {{ request()->routeIs('gym.equipment.*') ? 'active' : '' }}">
                        <i class="fas fa-wrench -icon"></i>
                        <span class="sidebar-text">All Equipment</span>
                        @if(request()->routeIs('gym.equipment.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link sidebardisabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-wrench sidebar-icon"></i>
                        <span class="sidebar-text">All Equipment</span>
                    </a>
                @endif
                <a href="#" class="sidebar-link">
                    <i class="fas fa-calendar-check sidebar-icon"></i>
                    <span class="sidebar-text">Maintenance</span>
                    <span class="sidebar-badge bg-warning">Due</span>
                </a>
            </div>

            <!-- Financial Management -->
            @if(auth()->user()->hasRole(['super-admin', 'admin', 'accountant']))
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-dollar-sign sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Financial</span>
                </div>
                @if(Route::has('gym.gym-revenues.index'))
                    <a href="{{ route('gym.gym-revenues.index') }}" class="sidebar-link {{ request()->routeIs('gym.gym-revenues.*') ? 'active' : '' }}">
                        <i class="fas fa-coins sidebar-icon"></i>
                        <span class="sidebar-text">Revenue</span>
                        @if(request()->routeIs('gym.gym-revenues.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-coins sidebar-icon"></i>
                        <span class="sidebar-text">Revenue</span>
                    </a>
                @endif

                @if(Route::has('expenses.index'))
                    <a href="{{ route('expenses.index') }}" class="sidebar-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                        <i class="fas fa-receipt sidebar-icon"></i>
                        <span class="sidebar-text">Expenses</span>
                        @if(request()->routeIs('expenses.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-receipt sidebar-icon"></i>
                        <span class="sidebar-text">Expenses</span>
                    </a>
                @endif

                @if(Route::has('payments.index'))
                    <a href="{{ route('payments.index') }}" class="sidebar-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                        <i class="fas fa-credit-card sidebar-icon"></i>
                        <span class="sidebar-text">Payments</span>
                        @if(request()->routeIs('payments.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-credit-card sidebar-icon"></i>
                        <span class="sidebar-text">Payments</span>
                    </a>
                @endif

                @if(Route::has('gym.reports'))
                    <a href="{{ route('gym.reports') }}" class="sidebar-link {{ request()->routeIs('gym.reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar sidebar-icon"></i>
                        <span class="sidebar-text">Financial Reports</span>
                        @if(request()->routeIs('gym.reports.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-chart-bar sidebar-icon"></i>
                        <span class="sidebar-text">Financial Reports</span>
                    </a>
                @endif
            </div>
            @endif

            <!-- Administration -->
            @if(auth()->user()->hasRole(['super-admin', 'admin']))
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-cogs sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Administration</span>
                </div>
                @if(Route::has('users.index'))
                    <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog sidebar-icon"></i>
                        <span class="sidebar-text">Users</span>
                        @if(request()->routeIs('users.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-users-cog sidebar-icon"></i>
                        <span class="sidebar-text">Users</span>
                    </a>
                @endif

                @if(Route::has('roles.index'))
                    <a href="{{ route('roles.index') }}" class="sidebar-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                        <i class="fas fa-user-shield sidebar-icon"></i>
                        <span class="sidebar-text">Roles & Permissions</span>
                        @if(request()->routeIs('roles.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-user-shield sidebar-icon"></i>
                        <span class="sidebar-text">Roles & Permissions</span>
                    </a>
                @endif

                @if(Route::has('settings.index'))
                    <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog sidebar-icon"></i>
                        <span class="sidebar-text">Settings</span>
                        @if(request()->routeIs('settings.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-cog sidebar-icon"></i>
                        <span class="sidebar-text">Settings</span>
                    </a>
                @endif
            </div>
            @endif

            <!-- Multi-Tenant Management (Super Admin Only) -->
            @if(auth()->user()->hasRole('super-admin'))
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-building sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Tenant Management</span>
                </div>
                @if(Route::has('admin.tenants.index'))
                    <a href="{{ route('admin.tenants.index') }}" class="sidebar-link {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}">
                        <i class="fas fa-city sidebar-icon"></i>
                        <span class="sidebar-text">Gym Branches</span>
                        @if(request()->routeIs('admin.tenants.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-city sidebar-icon"></i>
                        <span class="sidebar-text">Gym Branches</span>
                    </a>
                @endif

                @if(Route::has('admin.tenant-subscriptions.index'))
                    <a href="{{ route('admin.tenant-subscriptions.index') }}" class="sidebar-link {{ request()->routeIs('admin.tenant-subscriptions.*') ? 'active' : '' }}">
                        <i class="fas fa-credit-card sidebar-icon"></i>
                        <span class="sidebar-text">Subscriptions</span>
                        @if(request()->routeIs('admin.tenant-subscriptions.*'))
                            <span class="sidebar-indicator"></span>
                        @endif
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-credit-card sidebar-icon"></i>
                        <span class="sidebar-text">Subscriptions</span>
                    </a>
                @endif
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="sidebar-section">
                <div class="sidebar-section-header">
                    <i class="fas fa-bolt sidebar-section-icon"></i>
                    <span class="sidebar-section-title">Quick Actions</span>
                </div>
                @if(Route::has('gym.members.create'))
                    <a href="{{ route('gym.members.create') }}" class="sidebar-link">
                        <i class="fas fa-user-plus sidebar-icon text-success"></i>
                        <span class="sidebar-text">Add Member</span>
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-user-plus sidebar-icon text-success"></i>
                        <span class="sidebar-text">Add Member</span>
                    </a>
                @endif

                @if(Route::has('gym.fitness-classes.create'))
                    <a href="{{ route('gym.fitness-classes.create') }}" class="sidebar-link">
                        <i class="fas fa-plus-circle sidebar-icon text-primary"></i>
                        <span class="sidebar-text">Schedule Class</span>
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-plus-circle sidebar-icon text-primary"></i>
                        <span class="sidebar-text">Schedule Class</span>
                    </a>
                @endif

                @if(Route::has('gym.gym-revenues.create'))
                    <a href="{{ route('gym.gym-revenues.create') }}" class="sidebar-link">
                        <i class="fas fa-coins sidebar-icon text-warning"></i>
                        <span class="sidebar-text">Record Payment</span>
                    </a>
                @else
                    <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                        <i class="fas fa-coins sidebar-icon text-warning"></i>
                        <span class="sidebar-text">Record Payment</span>
                    </a>
                @endif
            </div>

            <!-- User Profile -->
            <div class="sidebar-section sidebar-user-section">
                <div class="sidebar-user-info">
                    <div class="sidebar-user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="sidebar-user-details">
                        <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                        <div class="sidebar-user-role">
                            @foreach(auth()->user()->roles as $role)
                                <span class="badge badge-secondary">{{ ucfirst($role->name) }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="sidebar-user-actions">
                    @if(Route::has('profile.edit'))
                        <a href="{{ route('profile.edit') }}" class="sidebar-link">
                            <i class="fas fa-user-edit sidebar-icon"></i>
                            <span class="sidebar-text">Profile</span>
                        </a>
                    @else
                        <a href="#" class="sidebar-link disabled" aria-disabled="true" title="Unavailable">
                            <i class="fas fa-user-edit sidebar-icon"></i>
                            <span class="sidebar-text">Profile</span>
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="sidebar-link btn btn-link p-0 w-100 text-left">
                            <i class="fas fa-sign-out-alt sidebar-icon"></i>
                            <span class="sidebar-text">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </nav>
</aside>

@push('styles')
<style>
/* Gym-specific sidebar styles */
.sidebar-wrapper {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.sidebar-brand .brand-text {
    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: bold;
}

/* Logo sizing & brand layout */
.sidebar-brand {
    padding: 1rem 1.25rem;
}
.sidebar-brand-link {
    gap: 0.5rem;
    text-decoration: none;
}
.sidebar-logo {
    width: 44px;
    height: 44px;
    display: inline-block;
    object-fit: contain;
    filter: drop-shadow(0 1px 2px rgba(0,0,0,0.25));
}
.sidebar-brand .brand-text {
    font-size: 1.05rem;
    margin-left: 0.5rem;
    color: white; /* fallback */
}

@media (max-width: 768px) {
    .sidebar-logo { width: 36px; height:36px; }
    .sidebar-brand .brand-text { font-size: 0.95rem; }
}

.sidebar-section-header {
    color: rgba(255, 255, 255, 0.9);
    padding: 1rem 1.5rem 0.5rem;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 0.5rem;
}

.sidebar-section-icon {
    margin-right: 0.5rem;
    color: #4ecdc4;
}

.sidebar-link {
    color: rgba(255, 255, 255, 0.8);
    padding: 0.75rem 1.5rem;
    border-radius: 0;
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    align-items: center;
}

.sidebar-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    text-decoration: none;
    transform: translateX(5px);
}

.sidebar-link.active {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border-left: 4px solid #4ecdc4;
}

.sidebar-icon {
    width: 20px;
    text-align: center;
    margin-right: 0.75rem;
    font-size: 1rem;
}

.sidebar-text {
    flex-grow: 1;
}

.sidebar-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
}

.sidebar-indicator {
    position: absolute;
    right: 1rem;
    width: 8px;
    height: 8px;
    background: #4ecdc4;
    border-radius: 50%;
}

.sidebar-user-section {
    margin-top: auto;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 1rem;
}

.sidebar-user-info {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: white;
}

.sidebar-user-avatar i {
    font-size: 2rem;
    margin-right: 0.75rem;
    color: #4ecdc4;
}

.sidebar-user-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.sidebar-user-role .badge {
    font-size: 0.65rem;
    background: rgba(255, 255, 255, 0.2);
    color: white;
}
</style>
@endpush
<!-- Enhanced Top Navigation with Mobile Sidebar Toggle -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-lg">
    <div class="container-fluid">
        <!-- Mobile Sidebar Toggle -->
        <button class="mobile-sidebar-toggle btn btn-link text-white d-lg-none me-3" type="button" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Brand/Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo me-2">
            <span class="fw-bold">{{ config('app.name', 'Accounting System') }}</span>
        </a>

        <!-- Right side navigation items -->
        <div class="navbar-nav ms-auto d-flex flex-row align-items-center">
            <!-- Tenant Switcher -->
            @auth
                @if(Auth::user()->tenants()->count() > 0)
                    <div class="nav-item me-3">
                        @include('components.tenant-switcher')
                    </div>
                @endif
            @endauth
            
            <!-- Notifications -->
            <div class="nav-item dropdown me-3">
                <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        @php
                            try {
                                $notificationCount = auth()->user()->unreadNotifications()->count();
                            } catch (\Exception $e) {
                                $notificationCount = 0; // Fallback if notifications table doesn't exist
                            }
                        @endphp
                        {{ $notificationCount > 0 ? $notificationCount : '' }}
                        <span class="visually-hidden">unread notifications</span>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="notificationsDropdown" style="min-width: 300px;">
                    <li class="dropdown-header bg-primary text-white fw-bold">
                        <i class="fas fa-bell me-2"></i>Notifications
                        @if($notificationCount > 0)
                            <span class="badge bg-light text-primary ms-2">{{ $notificationCount }}</span>
                        @endif
                    </li>
                    <li><hr class="dropdown-divider m-0"></li>
                    
                    @php
                        $notifications = [];
                        try {
                            $notifications = auth()->user()->unreadNotifications()->limit(5)->get();
                        } catch (\Exception $e) {
                            // Fallback to static notifications if database table is not available
                        }
                    @endphp
                    
                    @if($notifications->count() > 0)
                        @foreach($notifications as $notification)
                            @php
                                $data = $notification->data;
                                $icon = $data['icon'] ?? 'fas fa-info-circle';
                                $type = $data['type'] ?? 'info';
                                $typeClass = $type === 'success' ? 'text-success' : 
                                           ($type === 'warning' ? 'text-warning' : 
                                           ($type === 'danger' ? 'text-danger' : 'text-info'));
                            @endphp
                            <li class="px-3 py-2 border-bottom">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <i class="{{ $icon }} {{ $typeClass }}"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        <p class="mb-0 small">{{ $data['message'] ?? 'New notification' }}</p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    @else
                        <!-- Fallback static notifications for demo -->
                        <li class="px-3 py-2">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-plus text-success"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <small class="text-muted">5 minutes ago</small>
                                    <p class="mb-0 small">New employee registered: John Doe</p>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="px-3 py-2">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-money-bill text-warning"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <small class="text-muted">1 hour ago</small>
                                    <p class="mb-0 small">Payment of $1,250 received</p>
                                </div>
                            </div>
                        </li>
                    @endif
                    
                    <li><hr class="dropdown-divider"></li>
                    <li class="text-center py-2">
                        <a href="#" class="btn btn-sm btn-outline-primary">View All Notifications</a>
                    </li>
                </ul>
            </div>

            <!-- Theme Toggle -->
            <div class="nav-item me-3">
                <button class="btn btn-outline-light btn-sm theme-toggle-btn" type="button" title="Toggle theme">
                    <i class="fas fa-moon"></i>
                </button>
            </div>

            <!-- User Dropdown -->
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-2">
                            @if(auth()->user()->profile_photo_url ?? false)
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="Avatar" class="rounded-circle" width="32" height="32">
                            @else
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 32px; height: 32px; font-size: 14px;">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="d-none d-md-block">
                            <div class="fw-semibold">{{ auth()->user()->name ?? 'User' }}</div>
                            <small class="text-muted">{{ auth()->user()->email ?? '' }}</small>
                        </div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="userDropdown">
                    <li class="dropdown-header">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar me-2">
                                @if(auth()->user()->profile_photo_url ?? false)
                                    <img src="{{ auth()->user()->profile_photo_url }}" alt="Avatar" class="rounded-circle" width="40" height="40">
                                @else
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="fw-bold">{{ auth()->user()->name ?? 'User' }}</div>
                                <small class="text-muted">{{ auth()->user()->email ?? '' }}</small>
                                @if(auth()->user()->getRoleNames()->isNotEmpty())
                                    <div class="mt-1">
                                        <span class="badge bg-secondary">{{ auth()->user()->getRoleNames()->first() }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user-edit me-2 text-primary"></i>Edit Profile
                        </a>
                    </li>
                    
                    <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#settingsModal">
                            <i class="fas fa-cog me-2 text-secondary"></i>Settings
                        </a>
                    </li>

                    <!-- Role Switcher (if user has multiple roles) -->
                    @if(auth()->user()->getRoleNames()->count() > 1)
                    <li><hr class="dropdown-divider"></li>
                    <li class="dropdown-header">Switch Role</li>
                    @foreach(auth()->user()->getRoleNames() as $role)
                        <li>
                            <a class="dropdown-item role-switch-item {{ session('active_role') === $role ? 'active' : '' }}" 
                               href="{{ route('role.switch', $role) }}" 
                               data-role="{{ $role }}">
                                <i class="fas fa-user-tag me-2"></i>{{ ucfirst((string) $role) }}
                                @if(session('active_role') === $role)
                                    <i class="fas fa-check ms-auto text-success"></i>
                                @endif
                            </a>
                        </li>
                    @endforeach
                    @endif

                    <li><hr class="dropdown-divider"></li>
                    
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay"></div>

<style>
/* Enhanced Navigation Styles */
.navbar {
    backdrop-filter: blur(10px);
    background: rgba(33, 37, 41, 0.95) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.mobile-sidebar-toggle {
    border: none !important;
    background: none !important;
    color: rgba(255, 255, 255, 0.9) !important;
    font-size: 1.25rem;
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.mobile-sidebar-toggle:hover {
    background: rgba(255, 255, 255, 0.1) !important;
    color: white !important;
    transform: scale(1.05);
}

.mobile-sidebar-toggle.active {
    background: rgba(255, 255, 255, 0.2) !important;
    color: white !important;
}

.navbar-brand {
    font-weight: 700;
    color: white !important;
    text-decoration: none;
    transition: all 0.3s ease;
}

.navbar-brand:hover {
    color: #007bff !important;
    transform: translateX(3px);
}

.logo {
    height: 32px;
    width: auto;
    filter: brightness(1.1);
}

/* Notification Badge Animation */
.nav-link .badge {
    animation: pulse-scale 2s infinite;
}

@keyframes pulse-scale {
    0%, 100% { transform: translateX(-50%) translateY(-50%) scale(1); }
    50% { transform: translateX(-50%) translateY(-50%) scale(1.1); }
}

/* Dropdown Enhancements */
.dropdown-menu {
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    animation: dropdown-fade-in 0.2s ease;
    overflow: hidden;
}

@keyframes dropdown-fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown-item {
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
    border-radius: 0;
}

.dropdown-item:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transform: translateX(3px);
}

.dropdown-item.active {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
}

.dropdown-header {
    padding: 1rem;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    font-weight: 600;
    border: none;
}

/* Theme Toggle Button */
.theme-toggle-btn {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.theme-toggle-btn:hover {
    transform: rotate(15deg) scale(1.1);
    box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
}

/* User Avatar Enhancements */
.user-avatar img,
.user-avatar div {
    border: 2px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
}

.user-avatar:hover img,
.user-avatar:hover div {
    border-color: #007bff;
    transform: scale(1.05);
}

/* Sidebar Overlay */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.sidebar-overlay.show {
    opacity: 1;
    visibility: visible;
}

/* Role Switcher Styling */
.role-switch-item {
    position: relative;
}

.role-switch-item.active {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    font-weight: 600;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .navbar-brand span {
        display: none;
    }
    
    .logo {
        height: 28px;
    }
    
    .dropdown-menu {
        margin-top: 0.5rem;
        width: 280px;
        max-width: 90vw;
    }
    
    .d-none.d-md-block {
        display: none !important;
    }
}

@media (max-width: 576px) {
    .dropdown-menu {
        width: 260px;
        left: auto !important;
        right: 0 !important;
    }
}

/* Dark Theme Support */
[data-theme="dark"] .navbar {
    background: rgba(17, 24, 39, 0.95) !important;
    border-bottom-color: rgba(255, 255, 255, 0.1);
}

[data-theme="dark"] .dropdown-menu {
    background: #1f2937;
    border-color: rgba(255, 255, 255, 0.1);
}

[data-theme="dark"] .dropdown-item {
    color: #e5e7eb;
}

[data-theme="dark"] .dropdown-item:hover {
    background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
    color: white;
}

/* Animation for notification items */
.dropdown-menu li:nth-child(n+3):nth-child(-n+6) {
    animation: slide-in-left 0.3s ease forwards;
    opacity: 0;
    animation-delay: calc(0.1s * (var(--i, 1)));
}

@keyframes slide-in-left {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>
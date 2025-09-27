<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'My App'))</title>
@include('layouts.navigation')

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>   
    <!--custom css-->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!-- app.css -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- app.js -->
    <script src="{{ asset('js/app.js') }}"></script>    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/solid.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/brands.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/v4-shims.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/regular.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/svg-with-js.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/v4-shims.min.css">
    <!-- Custom CSS -->
    
    <style>
        body {
            overflow-x: hidden;
        }
        .sidebar {
            min-height: 100vh;
            background: #171819;
            color: #6586a7;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: .75rem 1rem;
            border-radius: .5rem;
            margin-bottom: .25rem;
        }
        .sidebar a.active,
        .sidebar a:hover {
            background: #495057;
            color: #fff;
        }
        .sidebar .brand {
            font-weight: bold;
            padding: 1rem;
            display: block;
            color: #fff;
            text-decoration: none;
            border-bottom: 1px solid #495057;
            margin-bottom: 1rem;
        }
        footer {
            background: #020f1b;
            font-size: large;
            color: white;
            padding: 1rem;
            text-align: center; 
        }
        .stat-card {
            border-left: 4px solid #0d6efd;
            background: #cbded1;
            border-radius: .5rem;
        }
        .stat-card h6 {
            font-size: 1rem;
            margin-bottom: .5rem;
            color: #5e7890;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .stat-sub {
            color: #6c757d;
        }           
    </style>

    @stack('styles')
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar">
            <a href="{{ url('/') }}" class="brand">
                <i class="fa-solid fa-chart-line me-2"></i> {{ config('app.name', 'My App') }}
            </a>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high me-2"></i> Dashboard
            </a>
            <a href="{{ route('workers.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users me-2"></i> Employees
            </a>
             <a href="{{ route('clients.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users me-2"></i> Clients
            </a>
            <a href="{{ route('projects.index') }}" class="{{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <i class="fa-solid fa-money-bill me-2"></i> Projects
            </a>
            <a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <i class="fa-solid fa-money-bill me-2"></i> Payments
            </a>
            <a href="{{ route('expenses.index') }}" class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt me-2"></i> Expenses
            </a>
            <a href="{{ route('incomes.index') }}" class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <i class="fa-solid fa-arrow-right-arrow-left me-2"></i> Revenues
            </a>
              <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <i class="fa-solid fa-arrow-right-arrow-left me-2"></i> Transactions
            </a>
             <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-shield me-2"></i> Roles
            </a>
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user me-2"></i> Users
            </a>    
                <!-- New Permissions Link -->   
                <a href="{{ route('permissions.index') }}" class="{{ request()->routeIs('permissions.*') ? 'active' : '' }}">   
                <i class="fa-solid fa-lock me-2"></i> Permissions
            </a>
            <!-- End New Permissions Link -->
                
            <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear me-2"></i> Settings
            </a>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            @yield('content')
        </main>
    </div>
</div>

<footer class="text-center py-3 mt-auto">
    <small class="text-muted">&copy; {{ date('Y') }} {{ config('app.name', 'My App') }}</small>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@stack('scripts')
</body>
</html>

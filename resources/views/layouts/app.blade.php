<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SiteLedger - Construction Finance Management System | Track Projects & Payments')</title>
    
    <!-- Theme color for mobile browsers -->
    <meta name="theme-color" content="#ffffff" id="theme-color-meta">
    
    <!-- Favicon and App Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo/siteledger-favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo/siteledger-favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/siteledger-icon.svg') }}">
    
    <!-- Meta Information -->
    <meta name="description" content="@yield('meta_description', 'Professional construction finance management software for Rwanda. Track projects, monitor income/expenses, manage worker payments, and generate comprehensive financial reports in RWF currency.')">
    <meta name="keywords" content="@yield('meta_keywords', 'construction finance management, project management Rwanda, construction accounting, worker payments, expense tracking, financial reports, RWF, construction ledger, project budgeting, construction analytics')"
    <meta name="author" content="SiteLedger">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    
    <!-- Open Graph / Social Media -->
    <meta property="og:title" content="@yield('og_title', 'SiteLedger - Construction Finance Management System')">
    <meta property="og:description" content="@yield('og_description', 'Professional construction finance management software for Rwanda. Track projects, monitor expenses, and manage worker payments with comprehensive reporting.')">
    <meta property="og:image" content="@yield('og_image', asset('images/logo/siteledger-logo.svg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name', 'SiteLedger') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'SiteLedger - Construction Finance Management System')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Professional construction finance management software for Rwanda. Track projects, monitor expenses, and manage worker payments.')">

    <!-- Fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/solid.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/brands.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>   

    
    <style>
        body {
            overflow-x: hidden;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        /* Theme-aware body background */
        .theme-light body {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 50%, #a5d6a7 100%) !important;
            background-attachment: fixed !important;
        }
        
        .theme-dark body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%) !important;
            background-attachment: fixed !important;
        }
        
        footer {
            background: var(--bg-tertiary);
            color: var(--text-muted);
            font-size: xx-large;
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
        
        /* CRITICAL: Navigation Bar Fixed Positioning */
        nav.navbar {
            position: fixed !important;
            top: 0 !important;
            left: 220px !important;
            right: 0 !important;
            z-index: 1030 !important;
            margin: 0 !important;
            width: calc(100% - 220px) !important;
        }
        
        @media (max-width: 768px) {
            nav.navbar {
                left: 0 !important;
                width: 100% !important;
            }
        }
        
        /* Adjust body padding for fixed navbar */
        body.bg-light {
            padding-top: 60px !important;
        }
        
        .main-wrapper {
            margin-top: 0 !important;
        }
        
        /* Role Switcher Styling */
        .dropdown-menu {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .dropdown-item.active {
            background-color: #e3f2fd !important;
            border-left: 3px solid #0d6efd;
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        #roleSwitcher .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        
        .logo {
            height: 24px;
            width: auto;
            display: inline-block;
            vertical-align: middle;
        }

        .logo-responsive {
            height: 32px;
        }

        @media (max-width: 768px) {
            .logo-responsive {
                height: 20px;
            }

            /* Global Form Styling — consistent look across Bootstrap/Tailwind mixes */
            .main-wrapper form label {
                font-weight: 600;
                color: #374151; /* gray-700 */
                margin-bottom: .25rem;
                display: inline-block;
            }
            .main-wrapper form small,
            .main-wrapper form .form-text {
                color: #6b7280; /* gray-500 */
            }
            /* Target both generic inputs and Bootstrap form-control */
            .main-wrapper form input[type="text"],
            .main-wrapper form input[type="email"],
            .main-wrapper form input[type="number"],
            .main-wrapper form input[type="date"],
            .main-wrapper form input[type="datetime-local"],
            .main-wrapper form input[type="password"],
            .main-wrapper form select,
            .main-wrapper form textarea,
            .main-wrapper form .form-control,
            .main-wrapper form .form-select {
                background-color: #ffffff;
                border: 1px solid #d1d5db; /* gray-300 */
                color: #111827; /* gray-900 */
                border-radius: .5rem; /* rounded-xl */
                padding: .575rem .85rem;
                line-height: 1.5;
                box-shadow: 0 1px 2px rgba(0,0,0,0.03);
                transition: border-color .15s ease, box-shadow .15s ease;
            }
            .main-wrapper form input[type="text"]:focus,
            .main-wrapper form input[type="email"]:focus,
            .main-wrapper form input[type="number"]:focus,
            .main-wrapper form input[type="date"]:focus,
            .main-wrapper form input[type="datetime-local"]:focus,
            .main-wrapper form input[type="password"]:focus,
            .main-wrapper form select:focus,
            .main-wrapper form textarea:focus,
            .main-wrapper form .form-control:focus,
            .main-wrapper form .form-select:focus {
                outline: none;
                border-color: #6366f1; /* indigo-500 */
                box-shadow: 0 0 0 3px rgba(99,102,241,0.18);
            }
            .main-wrapper form .sl-invalid,
            .main-wrapper form .is-invalid {
                border-color: #ef4444 !important; /* red-500 */
                box-shadow: 0 0 0 3px rgba(239,68,68,0.15) !important;
            }
            .main-wrapper form .help-error {
                color: #b91c1c; /* red-700 */
                font-size: .8125rem;
                margin-top: .25rem;
            }
            .main-wrapper form .form-row { margin-bottom: 1rem; }
            .main-wrapper form .form-actions { margin-top: 1rem; display: flex; gap: .5rem; }
        }

        @media (min-width: 1200px) {
            .logo-responsive {
                height: 40px;
            }
        }
    </style>
<!-- jQuery (keep for other components) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable for any tables with the 'data-table' class
        $('.data-table').each(function() {
            if (!$.fn.DataTable.isDataTable(this)) {
                $(this).DataTable({
                    pageLength: 10,
                    responsive: true,
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    }
                });
            }
        });
        
        // Initialize specific tables by ID with safety checks
        if ($('#accountsTable').length && !$.fn.DataTable.isDataTable('#accountsTable')) {
            $('#accountsTable').DataTable({
                pageLength: 10,
                responsive: true
            });
        }
        
        if ($('#myTable').length && !$.fn.DataTable.isDataTable('#myTable')) {
            $('#myTable').DataTable({
                pageLength: 5,
                responsive: true
            });
        }
    });
</script>

@vite(['resources/css/app.css', 'resources/js/app.js'])


    @stack('styles')
    @stack('meta')
</head>
<body class="bg-light">

<!-- Navigation Bar -->
@include('layouts.navigation')

<!-- Include the new polished sidebar -->
@include('layouts.sidebar')

<!-- Main Content Wrapper -->
<div class="main-wrapper">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mx-4 mt-3 shadow-sm" role="alert" style="border-left: 4px solid #198754;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fs-5"></i>
                <div>
                    <strong>Success!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3 shadow-sm" role="alert" style="border-left: 4px solid #dc3545;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fs-5"></i>
                <div>
                    <strong>Error!</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mx-4 mt-3 shadow-sm" role="alert" style="border-left: 4px solid #ffc107;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-3 fs-5"></i>
                <div>
                    <strong>Warning!</strong> {{ session('warning') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show mx-4 mt-3 shadow-sm" role="alert" style="border-left: 4px solid #0dcaf0;">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-3 fs-5"></i>
                <div>
                    <strong>Info:</strong> {{ session('info') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Content Area -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer removed -->
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>



<!-- Auto-dismiss flash messages -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>

@stack('scripts')
<script>
// Global form UX helpers
document.addEventListener('DOMContentLoaded', function() {
    // Prevent double submission and show loading state
    document.body.addEventListener('submit', function(e) {
        const form = e.target.closest('form');
        if (!form) return;

        // Basic required validation hinting (browser handles actual block)
        const requiredFields = form.querySelectorAll('[required]');
        let firstInvalid = null;
        requiredFields.forEach(el => {
            if (el.disabled) return;
            // HTML5 validity
            if (!el.checkValidity()) {
                el.classList.add('sl-invalid');
                if (!firstInvalid) firstInvalid = el;
            }
            el.addEventListener('input', () => el.classList.remove('sl-invalid'), { once: true });
            el.addEventListener('change', () => el.classList.remove('sl-invalid'), { once: true });
        });
        if (firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Loading state on submit buttons
        const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        submitButtons.forEach(btn => {
            if (btn.dataset.loading === '1') return; // already set
            const originalText = btn.tagName === 'BUTTON' ? btn.innerHTML : btn.value;
            const loadingText = btn.getAttribute('data-loading-text') || 'Saving...';
            btn.dataset.originalText = originalText;
            btn.dataset.loading = '1';
            if (btn.tagName === 'BUTTON') {
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${loadingText}`;
            } else {
                btn.value = loadingText;
            }
            btn.disabled = true;
        });

        // Trim text fields before submit
        form.querySelectorAll('input[type="text"], textarea').forEach(el => {
            if (typeof el.value === 'string') el.value = el.value.trim();
        });
    }, true);

    // Client-side Generate helpers and formatting
    // Money-like inputs (amount, price, total, unit_price, line_total, salary, budget)
    const moneyNameRx = /(amount|price|total|unit_price|line_total|salary|budget)/i;
    document.body.addEventListener('blur', function(e) {
        const el = e.target;
        if (!(el instanceof HTMLInputElement)) return;
        if (el.type === 'number' || moneyNameRx.test(el.name || '')) {
            if (el.value !== '') {
                const num = Number(el.value.replace(/,/g, ''));
                if (!isNaN(num)) el.value = num.toFixed(2);
            }
        }
    }, true);

    // Generic trim on blur for text-like inputs
    document.body.addEventListener('blur', function(e) {
        const el = e.target;
        if (el && (el.matches && (el.matches('input[type="text"]') || el.matches('textarea')))) {
            el.value = (el.value || '').trim();
        }
    }, true);
});
</script>

<!-- Theme Management Script -->
<script src="{{ asset('js/theme.js') }}"></script>

<!-- Enhanced Sidebar Script -->
<script src="{{ asset('js/enhanced-sidebar.js') }}"></script>
<!-- Chart.js Theme Script -->
<script src="{{ asset('js/chart-theme.js') }}"></script>
</body>
</html>

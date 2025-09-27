@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>           


@php
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$today = Carbon::today();
$startOfMonth = $today->copy()->startOfMonth();
$startOfYear = $today->copy()->startOfYear();
$totalWorkers = 0;
$activeWorkers = 0;
$recentWorkers= collect();

if (Schema::hasTable('workers')) {
    $totalWorkers = \App\Models\Worker::count();
    // Try to use a status column if present, otherwise fallback to total
    if (Schema::hasColumn('workers', 'status')) {
        $activeWorkers= \App\Models\Worker::where('status', 'active')->count();
    } else {
        $activeWorkers = $totalWorkers;
    }
   $recentWorkers = \App\Models\Worker::latest()->limit(6)->get();
}

//Payments
$paymentsTotal = 0;
$paymentsThisMonth = 0;
$paymentsThisYear = 0;
$recentPayments = collect();

if (Schema::hasTable('payments')) {
    // safe column check; assume 'amount' exists usually
    $paymentsTotal = Schema::hasColumn('payments', 'amount') ? (float) DB::table('payments')->sum('amount') : 0;
    $paymentsThisMonth = Schema::hasColumn('payments', 'amount') ? (float) DB::table('payments')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount') : 0;
    $paymentsThisYear = Schema::hasColumn('payments', 'amount') ? (float) DB::table('payments')->whereBetween('created_at', [$startOfYear, $today->endOfDay()])->sum('amount') : 0;
    $recentPayments = \App\Models\Payment::latest()->limit(7)->get();
}
//Payments
$incomesTotal = 0;
$incomesThisMonth = 0;
$incomesThisYear = 0;
$recentincomes = collect();

if (Schema::hasTable('incomes')) {
    // safe column check; assume 'amount' exists usually
    $incomesTotal = Schema::hasColumn('incomes', 'amount') ? (float) DB::table('incomes')->sum('amount') : 0;
    $paymentsThisMonth = Schema::hasColumn('incomes', 'amount') ? (float) DB::table('incomes')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount') : 0;
    $paymentsThisYear = Schema::hasColumn('incomes', 'amount') ? (float) DB::table('incomes')->whereBetween('created_at', [$startOfYear, $today->endOfDay()])->sum('amount') : 0;
    $recentPayments = \App\Models\Payment::latest()->limit(7)->get();
}

// Expenses
$expensesTotal = 0;
$expensesThisMonth = 0;
$recentExpenses = collect();

if (Schema::hasTable('expenses')) {
    $expensesTotal = Schema::hasColumn('expenses', 'amount') ? (float) DB::table('expenses')->sum('amount') : 0;
    $expensesThisMonth = Schema::hasColumn('expenses', 'amount') ? (float) DB::table('expenses')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount') : 0;
    $recentExpenses = \App\Models\Expense::latest()->limit(7)->get();
}

// Transactions
$transactionsCount = 0;
$transactionsThisMonth = 0;
$transactionsTotal = null;
$recentTransactions = collect();

if (Schema::hasTable('transactions')) {
    $transactionsCount = DB::table('transactions')->count();
    $transactionsThisMonth = DB::table('transactions')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->count();
    if (Schema::hasColumn('transactions', 'amount')) {
        $transactionsTotal = (float) DB::table('transactions')->sum('amount');
    }
    // Try loading Eloquent if model exists
    if (class_exists(\App\Models\Transaction::class)) {
        $recentTransactions = \App\Models\Transaction::latest()->limit(7)->get();
    } else {
        $recentTransactions = DB::table('transactions')->latest()->limit(7)->get();
    }
}
// Transactions
$projectsCount = 0;
$projectsThisMonth = 0;
$projectsTotal = null;
$recentprojects = collect();

if (Schema::hasTable('transactions')) {
    $projectsCount = DB::table('projects')->count();
    $projectsThisMonth = DB::table('projects')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->count();
    if (Schema::hasColumn('projects', 'amount')) {
        $transactionsTotal = (float) DB::table('projects')->sum('amount');
    }
    // Try loading Eloquent if model exists
    if (class_exists(\App\Models\Project::class)) {
        $recentprojects = \App\Models\Project::latest()->limit(7)->get();
    } else {
        $recentprojects = DB::table('projects')->latest()->limit(7)->get();
    }
}
// Monthly series for last 6 months
$months = [];
$paymentsMonthly = [];
$expensesMonthly = [];
for ($i = 5; $i >= 0; $i--) {
    $dt = Carbon::now()->subMonths($i);
    $label = $dt->format('M Y');
    $months[] = $label;

    $mStart = $dt->copy()->startOfMonth();
    $mEnd = $dt->copy()->endOfMonth();

    $paymentsMonthly[] = Schema::hasTable('payments') && Schema::hasColumn('payments', 'amount')
        ? (float) DB::table('payments')->whereBetween('created_at', [$mStart, $mEnd])->sum('amount')
        : 0;

    $expensesMonthly[] = Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'amount')
        ? (float) DB::table('expenses')->whereBetween('created_at', [$mStart, $mEnd])->sum('amount')
        : 0;
}
@endphp

<div class="container-fluid py-4">
    <h1 class="mb-4">Dashboard</h1>
 
@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

    <div class="bg-white p-6 rounded shadow-md">
        <h2 class="text-xl font-semibold mb-4">Monthly Income</h2>
        <canvas id="incomeChart" height="100"></canvas>
    </div>
</div>

<script>
    const ctx = document.getElementById('incomeChart').getContext('2d');
    const incomeChart = new Chart(ctx, {
        type: 'bar', // bar, line, pie, etc.
        data: {
            labels: @json($months),
            datasets: [{
                label: 'Income Received ($)',
                data: @json($incomesTotal),
                backgroundColor: 'rgba(34, 197, 94, 0.7)',
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card p-3 chart-card">
                <h6>Payments — Last 6 months</h6>
                <canvas id="paymentsChart" height="120"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 chart-card">
                <h6>Expenses — Last 6 months</h6>
                <canvas id="expensesChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent records -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3 p-3">
                <h6>Recent Employees</h6>
                <ul class="list-group list-group-flush">
                    @if($recentWorkers->count())
                        @foreach($recentWorkers as $work)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $work->name ?? $work->full_name ?? '—' }}</strong>
                                    <div class="muted-small">{{ optional($work->created_at)->format('Y-m-d') ?? '—' }}</div>
                                </div>
                                <div class="muted-small">{{ $work->status ?? '' }}</div>
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item text-center text-muted">No employees found.</li>
                    @endif
                </ul>
            </div>

            <div class="card p-3">
                <h6>Recent Transactions</h6>
                <ul class="list-group list-group-flush">
                    @if($recentTransactions->count())
                        @foreach($recentTransactions as $t)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $t->type ?? ('#' . ($t->id ?? '—')) }}</strong>
                                    <div class="muted-small">{{ isset($t->amount) ? number_format($t->amount, 2) : '' }} • {{ optional($t->created_at)->diffForHumans() ?? '—' }}</div>
                                </div>
                                <div class="muted-small">{{ $t->status ?? '' }}</div>
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item text-center text-muted">No transactions found.</li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3 p-3">
                <h6>Recent Payments</h6>
                <ul class="list-group list-group-flush">
                    @if($recentPayments->count())
                        @foreach($recentPayments as $p)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ number_format($p->amount ?? 0, 2) }}</strong>
                                    <div class="muted-small">{{ $p->method ?? '—' }} • {{ optional($p->created_at)->format('Y-m-d') ?? '—' }}</div>
                                </div>
                                <div class="muted-small">{{ $p->reference ?? '' }}</div>
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item text-center text-muted">No payments found.</li>
                    @endif
                </ul>
            </div>

            <div class="card p-3">
                <h6>Recent Expenses</h6>
                <ul class="list-group list-group-flush">
                    @if($recentExpenses->count())
                        @foreach($recentExpenses as $e)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ number_format($e->amount ?? 0, 2) }}</strong>
                                    <div class="muted-small">{{ $e->category ?? '' }} • {{ optional($e->created_at)->format('Y-m-d') ?? '—' }}</div>
                                </div>
                                <div class="muted-small">{{ $e->vendor ?? '' }}</div>
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item text-center text-muted">No expenses found.</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js should be included in layouts.app; otherwise include CDN here -->
<script>
    (function() {
        const months = @json($months);
        const paymentsData = @json($paymentsMonthly);
        const expensesData = @json($expensesMonthly);

        function buildBarChart(ctxId, label, data, colorStops) {
            const el = document.getElementById(ctxId);
            if (!el) return;
            const ctx = el.getContext('2d');
            const gradient = ctx.createLinearGradient(0,0,0,200);
            // default gradient if none provided
            gradient.addColorStop(0, (colorStops && colorStops[0]) || 'rgba(54,162,235,0.85)');
            gradient.addColorStop(1, (colorStops && colorStops[1]) || 'rgba(54,162,235,0.15)');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: gradient,
                        borderRadius: 6,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const v = context.parsed.y ?? 0;
                                    return (typeof v === 'number') ? v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : v;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            buildBarChart('paymentsChart', 'Payments', paymentsData, ['rgba(75,192,192,0.85)', 'rgba(75,192,192,0.12)']);
            buildBarChart('expensesChart', 'Expenses', expensesData, ['rgba(255,99,132,0.85)', 'rgba(255,99,132,0.12)']);
        });
    })();
</script>
@endpush

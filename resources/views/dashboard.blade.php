{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@php
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$today = Carbon::today();
$startOfMonth = $today->copy()->startOfMonth();
$startOfYear = $today->copy()->startOfYear();

// Workers
$totalWorkers = Schema::hasTable('workers') ? \App\Models\Worker::count() : 0;
$activeWorkers = Schema::hasTable('workers') && Schema::hasColumn('workers', 'status')
    ? \App\Models\Worker::where('status','active')->count()
    : $totalWorkers;
$recentWorkers = Schema::hasTable('workers') ? \App\Models\Worker::latest()->limit(6)->get() : collect();

// Payments
$paymentsTotal = Schema::hasTable('payments') && Schema::hasColumn('payments','amount')
    ? DB::table('payments')->sum('amount')
    : 0;
$paymentsThisMonth = Schema::hasTable('payments') && Schema::hasColumn('payments','amount')
    ? DB::table('payments')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount')
    : 0;
$recentPayments = Schema::hasTable('payments') ? \App\Models\Payment::latest()->limit(7)->get() : collect();
// Transactions
$recentTransactions = Schema::hasTable('transactions') ? \App\Models\Transaction::latest()->limit(7)->get() : collect();    
$transactionsThisMonth = Schema::hasTable('transactions') && Schema::hasColumn('transactions','amount')     
    ? DB::table('transactions')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount')
    : 0;
$paymentsThisMonth = Schema::hasTable('payments') && Schema::hasColumn('payments','amount')
    ? DB::table('payments')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount')
    : 0;

// Incomes
$incomesTotal = Schema::hasTable('incomes') && Schema::hasColumn('incomes','amount_received')
    ? DB::table('incomes')->sum('amount_received')
    : 0;
$incomesThisMonth = Schema::hasTable('incomes') && Schema::hasColumn('incomes','amount_received')
    ? DB::table('incomes')->whereBetween('received_at', [$startOfMonth, $today->endOfDay()])->sum('amount_received')
    : 0;
$recentIncomes = Schema::hasTable('incomes') ? \App\Models\Income::latest()->limit(7)->get() : collect();

// Expenses
$expensesTotal = Schema::hasTable('expenses') && Schema::hasColumn('expenses','amount')
    ? DB::table('expenses')->sum('amount')
    : 0;
$expensesThisMonth = Schema::hasTable('expenses') && Schema::hasColumn('expenses','amount')
    ? DB::table('expenses')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount')
    : 0;
$recentExpenses = Schema::hasTable('expenses') ? \App\Models\Expense::latest()->limit(7)->get() : collect();

// Projects
$projectsCount = Schema::hasTable('projects') ? DB::table('projects')->count() : 0;
$projectsThisMonth = Schema::hasTable('projects') ? DB::table('projects')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->count() : 0;
$projectsTotal = Schema::hasTable('projects') && Schema::hasColumn('projects','budget')
    ? DB::table('projects')->sum('budget')
    : null;
$recentProjects = Schema::hasTable('projects') ? \App\Models\Project::latest()->limit(7)->get() : collect();        
// Monthly series for last 6 months
$months = [];
$paymentsMonthly = [];
$expensesMonthly = [];
$incomeMonthly = [];
for ($i=5;$i>=0;$i--) {
    $dt = Carbon::now()->subMonths($i);
    $months[] = $dt->format('M Y');

    $mStart = $dt->copy()->startOfMonth();
    $mEnd = $dt->copy()->endOfMonth();

    $paymentsMonthly[] = Schema::hasTable('payments') && Schema::hasColumn('payments','amount')
        ? DB::table('payments')->whereBetween('created_at',[$mStart,$mEnd])->sum('amount')
        : 0;

    $expensesMonthly[] = Schema::hasTable('expenses') && Schema::hasColumn('expenses','amount')
        ? DB::table('expenses')->whereBetween('created_at',[$mStart,$mEnd])->sum('amount')
        : 0;

    $incomeMonthly[] = Schema::hasTable('incomes') && Schema::hasColumn('incomes','amount_received')
        ? DB::table('incomes')->whereBetween('received_at',[$mStart,$mEnd])->sum('amount_received')
        : 0;
}
@endphp

@section('content')
<div class="container-fluid py-4">
    <h1 class="mb-4">Dashboard</h1>

   
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card p-3">
                <h6>Total Workers</h6>
                <div class="stat-value"><span data-count="{{ $totalWorkers ?? 0}}">{{ number_format($totalWorkers ?? 0) }}</span></div>
                <div class="stat-sub">Active: <span data-count="{{ $activeWorkers?? 0}}">{{ number_format($activeWorkers ?? 0) }}</span></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card p-3">
                <h6>Incomes (total)</h6>
                <div class="stat-value"><span data-count="{{ $incomesTotal }}" data-decimals="2">{{ number_format($incomesTotal, 2) }}</span></div>
                <div class="stat-sub">This month: <span data-count="{{ $incomesThisMonth }}" data-decimals="2">{{ number_format($incomesThisMonth, 2) }}</span></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card p-3">
                <h6>Expenses (total)</h6>
                <div class="stat-value"><span data-count="{{ $expensesTotal }}" data-decimals="2">{{ number_format($expensesTotal, 2) }}</span></div>
                <div class="stat-sub">This month: <span data-count="{{ $expensesThisMonth }}" data-decimals="2">{{ number_format($expensesThisMonth, 2) }}</span></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card p-3">
                <h6>Projects</h6>
                <div class="stat-value"><span data-count="{{ $projectsCount }}">{{ number_format($projectsCount) }}</span></div>
                <div class="stat-sub">This month: <span data-count="{{ $projectsThisMonth }}">{{ number_format($projectsThisMonth) }}</span></div>
                @if(!is_null($projectsTotal))
                    <div class="muted-small">Total amount: <span data-count="{{ $projectsTotal }}" data-decimals="2">{{ number_format($projectsTotal, 2) }}</span></div>
                @endif
            </div>
        </div>
    </div>  
</div>

<!-- repeat for Expenses and Projects -->

    {{-- Charts --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card p-3 chart-card">
                <h6>Income — Last 6 months</h6>
                <canvas id="incomeChart" height="120"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 chart-card">
                <h6>Payments — Last 6 months</h6>
                <canvas id="paymentsChart" height="120"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 chart-card">
                <h6>Expenses — Last 6 months</h6>
                <canvas id="expensesChart" height="120"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Records --}}
    <div class="row g-3">
        <x-dashboard-recent title="Recent Employees" :items="$recentWorkers" fields="name,status,created_at" />
        <x-dashboard-recent title="Recent Transactions" :items="$recentTransactions" fields="type,status,amount,created_at" />
        <x-dashboard-recent title="Recent Payments" :items="$recentPayments" fields="amount,method,reference,created_at" />
        <x-dashboard-recent title="Recent Expenses" :items="$recentExpenses" fields="amount,category,vendor,created_at" />
    </div>
</div>

{{-- Charts JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded',function(){
    const months = @json($months);
    const incomeData = @json($incomeMonthly);
    const paymentsData = @json($paymentsMonthly);
    const expensesData = @json($expensesMonthly);

    function buildChart(ctxId,label,data,color){
        const ctx = document.getElementById(ctxId).getContext('2d');
        new Chart(ctx,{
            type:'bar',
            data:{
                labels:months,
                datasets:[{label,data,backgroundColor:color}]
            },
            options:{responsive:true,scales:{y:{beginAtZero:true}}}
        });
    }

    buildChart('incomeChart','Income',incomeData,'rgba(34,197,94,0.7)');
    buildChart('paymentsChart','Payments',paymentsData,'rgba(75,192,192,0.7)');
    buildChart('expensesChart','Expenses',expensesData,'rgba(255,99,132,0.7)');
});
</script>
@endsection

{{-- resources/views/dashboard.blade.php --}}
@php $projectStats = $projectStats ?? collect(); @endphp

@extends('layouts.app')
@vite(['resources/css/app.css', 'resources/js/app.js'])

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
$projectsTotal = Schema::hasTable('projects') && Schema::hasColumn('projects','contract_value')
    ? DB::table('projects')->sum('contract_value')
    : null;
$recentProjects = Schema::hasTable('projects') ? \App\Models\Project::latest()->limit(7)->get() : collect();

// Monthly series for last 6 months
$months = [];
$paymentsMonthly = [];
$expensesMonthly = [];
$incomeMonthly = [];
for ($i = 5; $i >= 0; $i--) {
    $dt = Carbon::now()->subMonths($i);
    $months[] = $dt->format('M Y');

    $mStart = $dt->copy()->startOfMonth();
    $mEnd = $dt->copy()->endOfMonth();

    $paymentsMonthly[] = Schema::hasTable('payments') && Schema::hasColumn('payments','amount')
        ? DB::table('payments')->whereBetween('created_at', [$mStart, $mEnd])->sum('amount')
        : 0;

    $expensesMonthly[] = Schema::hasTable('expenses') && Schema::hasColumn('expenses','amount')
        ? DB::table('expenses')->whereBetween('created_at', [$mStart, $mEnd])->sum('amount')
        : 0;

    $incomeMonthly[] = Schema::hasTable('incomes') && Schema::hasColumn('incomes','amount_received')
        ? DB::table('incomes')->whereBetween('received_at', [$mStart, $mEnd])->sum('amount_received')
        : 0;
}
@endphp

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold leading-tight">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Overview of activity, finances and projects</p>
        </div>

        <div class="flex items-center gap-3">
            <form id="dashboardSearchForm" method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                <label for="q" class="sr-only">Search</label>
                <div class="relative">
                    <input id="q" name="q" type="search" value="{{ request('q') ?? '' }}"
                        placeholder="Search workers, payments, projects..."
                        class="pl-10 pr-4 py-2 rounded-lg border bg-white shadow-sm focus:ring-2 focus:ring-indigo-200 focus:outline-none w-56"
                        autocomplete="off" aria-label="Search dashboard">
                    <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/></svg>
                </div>

                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-sm">Search</button>
                <a href="{{ route('dashboard') }}" class="px-3 py-2 border rounded-lg text-gray-600 hover:bg-gray-50">Reset</a>
            </form>

            <a href="{{ route('projects.create') ?? '#' }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 5a1 1 0 0 1 1 1v3h3a1 1 0 1 1 0 2h-3v3a1 1 0 1 1-2 0v-3H6a1 1 0 1 1 0-2h3V6a1 1 0 0 1 1-1z"/></svg>
                New
            </a>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 border overflow-hidden transform hover:-translate-y-1 transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Advance payment</div>
                    <div class="text-2xl font-bold mt-1">{{ number_format($totalWorkers) }}</div>
                    <div class="text-xs text-gray-400 mt-1">Active: {{ number_format($activeWorkers) }}</div>
                </div>
                <div class="flex-shrink-0 text-indigo-600 bg-indigo-50 rounded-full p-3">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 border overflow-hidden transform hover:-translate-y-1 transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Incomes (total)</div>
                    <div class="text-2xl font-bold mt-1">{{ number_format($incomesTotal, 2) }}</div>
                    <div class="text-xs text-gray-400 mt-1">This month: {{ number_format($incomesThisMonth, 2) }}</div>
                </div>
                <div class="flex-shrink-0 text-green-600 bg-green-50 rounded-full p-3">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 border overflow-hidden transform hover:-translate-y-1 transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Expenses (total)</div>
                    <div class="text-2xl font-bold mt-1">{{ number_format($expensesTotal, 2) }}</div>
                    <div class="text-xs text-gray-400 mt-1">This month: {{ number_format($expensesThisMonth, 2) }}</div>
                </div>
                <div class="flex-shrink-0 text-red-600 bg-red-50 rounded-full p-3">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M6 6h12M6 14h12M6 18h12"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 border overflow-hidden transform hover:-translate-y-1 transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-500">Projects</div>
                    <div class="text-2xl font-bold mt-1">{{ number_format($projectsCount) }}</div>
                    <div class="text-xs text-gray-400 mt-1">This month: {{ number_format($projectsThisMonth) }}</div>
                    @if(!is_null($projectsTotal))
                        <div class="text-xs text-gray-400 mt-2">Budget: {{ number_format($projectsTotal, 2) }}</div>
                    @endif
                </div>
                <div class="flex-shrink-0 text-yellow-600 bg-yellow-50 rounded-full p-3">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2l3 6 6 .5-4.5 4 1 6L12 17l-5.5 2.5 1-6L3 8.5 9 8z"/></svg>
                </div>
            </div>
        </div>
    </div>
    {{-- Daily category stats --}}
    <div class="mb-6 bg-white rounded-lg shadow p-4">
        <h2 class="text-lg font-semibold mb-3">Daily totals by category</h2>

        @if(empty($dailyTotals))
            <p class="text-sm text-gray-500">No stats available.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-2 px-3 border-r text-sm text-gray-600">Date</th>
                            @foreach($categories as $cat)
                                <th class="py-2 px-3 border-r text-sm text-gray-600">{{ $cat }}</th>
                            @endforeach
                            <th class="py-2 px-3 text-sm text-gray-600">Daily Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyTotals as $day => $cats)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="py-2 px-3 align-top font-medium">{{ $day }}</td>

                                @php $rowTotal = 0; @endphp

                                @foreach($categories as $cat)
                                    @php
                                        $amount = isset($cats[$cat]) ? $cats[$cat] : 0;
                                        $rowTotal += $amount;
                                    @endphp
                                    <td class="py-2 px-3 text-sm">
                                        @if($amount > 0)
                                            <span class="inline-block px-2 py-1 rounded text-sm font-medium bg-gray-100">
                                                RWF {{ number_format($amount, 2) }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                @endforeach

                                <td class="py-2 px-3 text-sm font-semibold text-red-600">
                                    RWF {{ number_format($rowTotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    {{-- Project Stats Section --}}
<div class="mb-6 bg-white rounded-lg shadow p-4">
    <h2 class="text-lg font-semibold mb-3">Project Payment Summary</h2>

    @if ($projectStats->isEmpty())
        <p class="text-sm text-gray-500">No project stats available.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-2 px-3 border-r text-sm text-gray-600">#</th>
                        <th class="py-2 px-3 border-r text-sm text-gray-600">Project Name</th>
                        <th class="py-2 px-3 border-r text-sm text-gray-600">Total Amount</th>
                        <th class="py-2 px-3 border-r text-sm text-gray-600">Amount Paid</th>
                        <th class="py-2 px-3 text-sm text-gray-600">Amount Remaining</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projectStats as $index => $proj)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="py-2 px-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="py-2 px-3 font-medium text-gray-800">{{ $proj->project_name }}</td>
                            <td class="py-2 px-3 text-sm text-gray-700">
                                <span class="inline-block px-2 py-1 rounded bg-gray-100 font-semibold">
                                    RWF {{ number_format($proj->total_amount, 2) }}
                                </span>
                            </td>
                            <td class="py-2 px-3 text-sm text-green-700">
                                <span class="inline-block px-2 py-1 rounded bg-green-50 font-semibold">
                                    RWF {{ number_format($proj->amount_paid, 2) }}
                                </span>
                            </td>
                            <td class="py-2 px-3 text-sm text-red-700">
                                <span class="inline-block px-2 py-1 rounded bg-red-50 font-semibold">
                                    RWF {{ number_format($proj->amount_remaining, 2) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 border min-h-[260px] flex flex-col">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Income — Last 6 months</h3>
            <div class="flex-1">
                <canvas id="incomeChart" class="w-full h-48"></canvas>
            </div>
        </div>

        

        <div class="bg-white rounded-lg shadow-sm p-4 border min-h-[260px] flex flex-col">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Expenses — Last 6 months</h3>
            <div class="flex-1">
                <canvas id="expensesChart" class="w-full h-48"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent lists --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-gray-700">Recent Employees</h4>
                <a href="{{ route('workers.index') }}" class="text-indigo-600 text-sm">View all</a>
            </div>

            <ul class="divide-y">
                @forelse($recentWorkers as $work)
                    <li class="py-3 flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $work->full_name ?? ($work->name ?? '—') }}</div>
                            <div class="text-xs text-gray-400">{{ optional($work->created_at)->format('Y-m-d') ?? '—' }}</div>
                        </div>
                        <div class="text-xs px-3 py-1 rounded-full {{ ($work->status ?? '') === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($work->status ?? '—') }}
                        </div>
                    </li>
                @empty
                    <li class="py-6 text-center text-gray-500">No employees found.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-gray-700">Recent Transactions</h4>
                <a href="{{ route('transactions.index') ?? '#' }}" class="text-indigo-600 text-sm">View all</a>
            </div>

            <ul class="divide-y">
                @forelse($recentTransactions as $t)
                    <li class="py-3 flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $t->type ?? ('#' . ($t->id ?? '—')) }}</div>
                            <div class="text-xs text-gray-400">{{ isset($t->amount) ? number_format($t->amount,2) : '' }} • {{ optional($t->created_at)->diffForHumans() ?? '—' }}</div>
                        </div>
                        <div class="text-xs text-gray-500">{{ $t->status ?? '—' }}</div>
                    </li>
                @empty
                    <li class="py-6 text-center text-gray-500">No transactions found.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-gray-700">Recent Payments</h4>
                <a href="{{ route('payments.index') ?? '#' }}" class="text-indigo-600 text-sm">View all</a>
            </div>

            <ul class="divide-y">
                @forelse($recentPayments as $p)
                    <li class="py-3 flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ number_format($p->amount ?? 0, 2) }}</div>
                            <div class="text-xs text-gray-400">{{ $p->method ?? '—' }} • {{ optional($p->created_at)->format('Y-m-d') ?? '—' }}</div>
                        </div>
                        <div class="text-xs text-gray-500">{{ $p->reference ?? '—' }}</div>
                    </li>
                @empty
                    <li class="py-6 text-center text-gray-500">No payments found.</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-gray-700">Recent Expenses</h4>
                <a href="{{ route('expenses.index') ?? '#' }}" class="text-indigo-600 text-sm">View all</a>
            </div>

            <ul class="divide-y">
                @forelse($recentExpenses as $e)
                    <li class="py-3 flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ number_format($e->amount ?? 0, 2) }}</div>
                            <div class="text-xs text-gray-400">{{ $e->category ?? '' }} • {{ optional($e->created_at)->format('Y-m-d') ?? '—' }}</div>
                        </div>
                        <div class="text-xs text-gray-500">{{ $e->vendor ?? '—' }}</div>
                    </li>
                @empty
                    <li class="py-6 text-center text-gray-500">No expenses found.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="mt-6 text-sm text-gray-500">© {{ date('Y') }} {{ config('app.name', 'MyApp') }}</div>
</div>
@endsection

@push('styles')
<style>
    /* Fine tuning beyond Tailwind utilities */
    .shadow-sm { box-shadow: 0 6px 18px rgba(20,24,40,0.06); }
    .border { border: 1px solid rgba(17,24,39,0.04); }
    .min-h-\[260px\] { min-height: 260px; } /* for older Tailwind compilers */
    /* Small responsive tweaks */
    @media (max-width: 640px) {
        #q { width: 100% !important; }
    }
</style>
@endpush

@push('scripts')
<!-- Chart.js (loaded via CDN to keep view compact) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Dashboard search: debounce so we don't fire too many requests while typing.
    (function() {
        const input = document.getElementById('q');
        if (!input) return;
        let timeout = null;
        input.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                // If user clears field, auto-submit to reset filters; otherwise wait for manual submit.
                if (input.value.trim() === '') {
                    document.getElementById('dashboardSearchForm').submit();
                }
            }, 700);
        });
    })();

    // Chart shorthand
    const months = @json($months);
    const incomeData = @json($incomeMonthly);
    const paymentsData = @json($paymentsMonthly);
    const expensesData = @json($expensesMonthly);

    function createChart(canvasId, label, data, colorFrom, colorTo) {
        const el = document.getElementById(canvasId);
        if (!el) return;
        const ctx = el.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, el.height);
        gradient.addColorStop(0, colorFrom);
        gradient.addColorStop(1, colorTo);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label,
                    data,
                    fill: true,
                    backgroundColor: gradient,
                    borderColor: colorFrom,
                    tension: 0.35,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'white',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: 'rgba(15,23,42,0.04)' } }
                },
                interaction: { mode: 'index', intersect: false }
            }
        });
    }

    createChart('incomeChart', 'Income', incomeData, 'rgba(34,197,94,1)', 'rgba(34,197,94,0.08)');
    createChart('paymentsChart', 'Payments', paymentsData, 'rgba(59,130,246,1)', 'rgba(59,130,246,0.08)');
    createChart('expensesChart', 'Expenses', expensesData, 'rgba(239,68,68,1)', 'rgba(239,68,68,0.06)');

    // Small UI nicety: fade-in cards
    document.querySelectorAll('.bg-white.rounded-lg').forEach((el, i) => {
        el.style.opacity = 0;
        el.style.transform = 'translateY(6px)';
        setTimeout(() => {
            el.style.transition = 'opacity .4s ease, transform .4s cubic-bezier(.2,.9,.2,1)';
            el.style.opacity = 1;
            el.style.transform = 'translateY(0)';
        }, 60 * i);
    });
});
</script>
@endpush

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Dashboard
{
    /**
     * Return top-level KPI values used by the dashboard cards.
     *
     * @return array
     */
    public static function kpis(): array
    {
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();

        $totalWorkers = Schema::hasTable('workers') ? \App\Models\Worker::count() : 0;
        $activeWorkers = Schema::hasTable('workers') && Schema::hasColumn('workers', 'status')
            ? \App\Models\Worker::where('status', 'active')->count()
            : $totalWorkers;

        $incomesTotal = Schema::hasTable('incomes') && Schema::hasColumn('incomes', 'amount_received')
            ? DB::table('incomes')->sum('amount_received') : 0;
        $incomesThisMonth = Schema::hasTable('incomes') && Schema::hasColumn('incomes', 'amount_received')
            ? DB::table('incomes')->whereBetween('received_at', [$startOfMonth, $today->endOfDay()])->sum('amount_received') : 0;

        $expensesTotal = Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'amount')
            ? DB::table('expenses')->sum('amount') : 0;
        $expensesThisMonth = Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'amount')
            ? DB::table('expenses')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount') : 0;

        $projectsCount = Schema::hasTable('projects') ? DB::table('projects')->count() : 0;
        $projectsThisMonth = Schema::hasTable('projects')
            ? DB::table('projects')->whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->count() : 0;

        return [
            'workers' => [
                'total' => (int) $totalWorkers,
                'active' => (int) $activeWorkers,
            ],
            'incomes' => [
                'total' => (float) $incomesTotal,
                'this_month' => (float) $incomesThisMonth,
            ],
            'expenses' => [
                'total' => (float) $expensesTotal,
                'this_month' => (float) $expensesThisMonth,
            ],
            'projects' => [
                'count' => (int) $projectsCount,
                'this_month' => (int) $projectsThisMonth,
            ],
        ];
    }

    /**
     * Return recent rows for a given model class or table name.
     * Accepts an Eloquent model class string or table name.
     *
     * @param  string|object  $modelOrTable  E.g. \App\Models\Expense::class or 'expenses'
     * @param  int  $limit
     * @return \Illuminate\Support\Collection
     */
    public static function recent($modelOrTable, int $limit = 7)
    {
        // If they passed Eloquent class
        if (is_string($modelOrTable) && class_exists($modelOrTable)) {
            $model = $modelOrTable;
            if (Schema::hasTable((new $model)->getTable())) {
                return $model::latest()->limit($limit)->get();
            }
            return collect();
        }

        // If they passed a table name
        if (is_string($modelOrTable) && Schema::hasTable($modelOrTable)) {
            return DB::table($modelOrTable)->orderBy('created_at', 'desc')->limit($limit)->get();
        }

        // If they passed an Eloquent instance collection / object with ->latest method
        try {
            if (is_object($modelOrTable) && method_exists($modelOrTable, 'latest')) {
                return $modelOrTable->latest()->limit($limit)->get();
            }
        } catch (\Throwable $e) {
            // fallthrough
        }

        return collect();
    }

    /**
     * Build monthly series for the last N months (defaults to 6) for payments, expenses and incomes.
     *
     * @param  int  $monthsCount
     * @return array ['labels'=>[], 'payments'=>[], 'expenses'=>[], 'incomes'=>[]]
     */
    public static function monthlySeries(int $monthsCount = 6): array
    {
        $labels = [];
        $payments = [];
        $expenses = [];
        $incomes = [];

        for ($i = $monthsCount - 1; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $labels[] = $dt->format('M Y');

            $mStart = $dt->copy()->startOfMonth();
            $mEnd = $dt->copy()->endOfMonth();

            $payments[] = Schema::hasTable('payments') && Schema::hasColumn('payments', 'amount')
                ? (float) DB::table('payments')->whereBetween('created_at', [$mStart, $mEnd])->sum('amount')
                : 0.0;

            $expenses[] = Schema::hasTable('expenses') && Schema::hasColumn('expenses', 'amount')
                ? (float) DB::table('expenses')->whereBetween('created_at', [$mStart, $mEnd])->sum('amount')
                : 0.0;

            $incomes[] = Schema::hasTable('incomes') && Schema::hasColumn('incomes', 'amount_received')
                ? (float) DB::table('incomes')->whereBetween('received_at', [$mStart, $mEnd])->sum('amount_received')
                : 0.0;
        }

        return [
            'labels' => $labels,
            'payments' => $payments,
            'expenses' => $expenses,
            'incomes' => $incomes,
        ];
    }

    /**
     * Convenience method to return everything the dashboard view likely needs.
     *
     * @return array
     */
    public static function payload(): array
    {
        $kpis = self::kpis();
        $series = self::monthlySeries(6);

        return [
            'kpis' => $kpis,
            'months' => $series['labels'],
            'paymentsMonthly' => $series['payments'],
            'expensesMonthly' => $series['expenses'],
            'incomeMonthly' => $series['incomes'],
            'recentWorkers' => self::recent(\App\Models\Worker::class, 6),
            'recentPayments' => self::recent(\App\Models\Payment::class, 7),
            'recentExpenses' => self::recent(\App\Models\Expense::class, 7),
            'recentTransactions' => self::recent(\App\Models\Transaction::class, 7),
        ];
    }
}

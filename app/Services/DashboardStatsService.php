<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Project;

class DashboardStatsService
{
    /**
     * Check if table and column exist
     */
    private function has(string $table, ?string $column = null): bool
    {
        if (!Schema::hasTable($table)) {
            return false;
        }
        return $column ? Schema::hasColumn($table, $column) : true;
    }

    /**
     * Get daily income/expense statistics for the last 30 days
     * Returns data for charts showing trends
     */
    public function getDailyStats($days = 30)
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($days - 1);

        $dailyData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $endDate->copy()->subDays($i);
            $dateStr = $date->format('Y-m-d');

            $incomeAmount = 0;
            $expenseAmount = 0;

            if ($this->has('incomes', 'amount_received')) {
                $incomeAmount = Income::whereDate('received_at', $dateStr)
                    ->sum('amount_received');
            }

            if ($this->has('expenses', 'amount')) {
                $expenseAmount = Expense::whereDate('date', $dateStr)
                    ->sum('amount');
            }

            $dailyData[] = [
                'date' => $dateStr,
                'date_formatted' => $date->format('M d'),
                'income' => (float) $incomeAmount,
                'expense' => (float) $expenseAmount,
                'balance' => (float) ($incomeAmount - $expenseAmount),
            ];
        }

        return $dailyData;
    }

    /**
     * Get income totals by category for the current period
     */
    public function getIncomeByCategory($startDate = null, $endDate = null)
    {
        if (!$this->has('incomes')) {
            return [];
        }

        $startDate = $startDate ?? Carbon::today()->startOfMonth();
        $endDate = $endDate ?? Carbon::today()->endOfDay();

        return DB::table('incomes')
            ->select(
                DB::raw('COALESCE(projects.name, "Uncategorized") as category'),
                DB::raw('SUM(incomes.amount_received) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->leftJoin('projects', 'incomes.project_id', '=', 'projects.id')
            ->whereBetween('incomes.received_at', [$startDate, $endDate])
            ->groupBy('incomes.project_id', 'projects.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn($item) => [
                'category' => $item->category,
                'total' => (float) $item->total,
                'count' => $item->count,
            ])
            ->toArray();
    }

    /**
     * Get expense totals by category
     */
    public function getExpenseByCategory($startDate = null, $endDate = null)
    {
        if (!$this->has('expenses')) {
            return [];
        }

        $startDate = $startDate ?? Carbon::today()->startOfMonth();
        $endDate = $endDate ?? Carbon::today()->endOfDay();

        return DB::table('expenses')
            ->select(
                'category',
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(fn($item) => [
                'category' => $item->category ?? 'Uncategorized',
                'total' => (float) $item->total,
                'count' => $item->count,
            ])
            ->toArray();
    }

    /**
     * Get weekly statistics (last 12 weeks)
     */
    public function getWeeklyStats($weeks = 12)
    {
        $endDate = Carbon::today();
        $weeklyData = [];

        for ($i = $weeks - 1; $i >= 0; $i--) {
            $weekEnd = $endDate->copy()->subWeeks($i)->endOfWeek();
            $weekStart = $weekEnd->copy()->startOfWeek();

            $incomeAmount = 0;
            $expenseAmount = 0;

            if ($this->has('incomes', 'amount_received')) {
                $incomeAmount = Income::whereBetween('received_at', [$weekStart, $weekEnd])
                    ->sum('amount_received');
            }

            if ($this->has('expenses', 'amount')) {
                $expenseAmount = Expense::whereBetween('date', [$weekStart, $weekEnd])
                    ->sum('amount');
            }

            $weeklyData[] = [
                'week_start' => $weekStart->format('Y-m-d'),
                'week_end' => $weekEnd->format('Y-m-d'),
                'week_label' => $weekStart->format('M d') . ' - ' . $weekEnd->format('M d'),
                'income' => (float) $incomeAmount,
                'expense' => (float) $expenseAmount,
                'balance' => (float) ($incomeAmount - $expenseAmount),
            ];
        }

        return $weeklyData;
    }

    /**
     * Get comprehensive financial summary
     */
    public function getFinancialSummary()
    {
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfToday = $today->endOfDay();
        $startOfYear = $today->copy()->startOfYear();

        $summary = [
            'today' => [
                'income' => 0,
                'expense' => 0,
                'balance' => 0,
            ],
            'this_month' => [
                'income' => 0,
                'expense' => 0,
                'balance' => 0,
            ],
            'this_year' => [
                'income' => 0,
                'expense' => 0,
                'balance' => 0,
            ],
            'all_time' => [
                'income' => 0,
                'expense' => 0,
                'balance' => 0,
            ],
        ];

        // Today
        if ($this->has('incomes', 'amount_received')) {
            $summary['today']['income'] = (float) Income::whereDate('received_at', $today)
                ->sum('amount_received');
        }
        if ($this->has('expenses', 'amount')) {
            $summary['today']['expense'] = (float) Expense::whereDate('date', $today)
                ->sum('amount');
        }
        $summary['today']['balance'] = $summary['today']['income'] - $summary['today']['expense'];

        // This Month
        if ($this->has('incomes', 'amount_received')) {
            $summary['this_month']['income'] = (float) Income::whereBetween('received_at', [$startOfMonth, $endOfToday])
                ->sum('amount_received');
        }
        if ($this->has('expenses', 'amount')) {
            $summary['this_month']['expense'] = (float) Expense::whereBetween('date', [$startOfMonth, $endOfToday])
                ->sum('amount');
        }
        $summary['this_month']['balance'] = $summary['this_month']['income'] - $summary['this_month']['expense'];

        // This Year
        if ($this->has('incomes', 'amount_received')) {
            $summary['this_year']['income'] = (float) Income::whereBetween('received_at', [$startOfYear, $endOfToday])
                ->sum('amount_received');
        }
        if ($this->has('expenses', 'amount')) {
            $summary['this_year']['expense'] = (float) Expense::whereBetween('date', [$startOfYear, $endOfToday])
                ->sum('amount');
        }
        $summary['this_year']['balance'] = $summary['this_year']['income'] - $summary['this_year']['expense'];

        // All Time
        if ($this->has('incomes', 'amount_received')) {
            $summary['all_time']['income'] = (float) Income::sum('amount_received');
        }
        if ($this->has('expenses', 'amount')) {
            $summary['all_time']['expense'] = (float) Expense::sum('amount');
        }
        $summary['all_time']['balance'] = $summary['all_time']['income'] - $summary['all_time']['expense'];

        return $summary;
    }

    /**
     * Get top performing projects by income
     */
    public function getTopProjects($limit = 5)
    {
        if (!$this->has('projects') || !$this->has('incomes')) {
            return [];
        }

        return DB::table('projects')
            ->leftJoin('incomes', 'projects.id', '=', 'incomes.project_id')
            ->select(
                'projects.id',
                'projects.name',
                DB::raw('COALESCE(SUM(incomes.amount_received), 0) as income'),
                DB::raw('COALESCE(projects.contract_value, 0) as target'),
                DB::raw('CASE WHEN projects.contract_value > 0 THEN ROUND((COALESCE(SUM(incomes.amount_received), 0) / projects.contract_value) * 100, 2) ELSE 0 END as completion_percent')
            )
            ->groupBy('projects.id', 'projects.name', 'projects.contract_value')
            ->orderByDesc('income')
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'income' => (float) $item->income,
                'target' => (float) $item->target,
                'completion_percent' => (float) $item->completion_percent,
            ])
            ->toArray();
    }

    /**
     * Get cash flow analysis (income vs expenses trend)
     */
    public function getCashFlowAnalysis($months = 6)
    {
        $endDate = Carbon::today();
        $cashFlow = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $monthEnd = $endDate->copy()->subMonths($i)->endOfMonth();
            $monthStart = $monthEnd->copy()->startOfMonth();

            $incomeAmount = 0;
            $expenseAmount = 0;

            if ($this->has('incomes', 'amount_received')) {
                $incomeAmount = Income::whereBetween('received_at', [$monthStart, $monthEnd])
                    ->sum('amount_received');
            }

            if ($this->has('expenses', 'amount')) {
                $expenseAmount = Expense::whereBetween('date', [$monthStart, $monthEnd])
                    ->sum('amount');
            }

            $cashFlow[] = [
                'month' => $monthStart->format('M Y'),
                'month_short' => $monthStart->format('M'),
                'income' => (float) $incomeAmount,
                'expense' => (float) $expenseAmount,
                'net_cash_flow' => (float) ($incomeAmount - $expenseAmount),
                'margin' => $incomeAmount > 0 ? round((($incomeAmount - $expenseAmount) / $incomeAmount) * 100, 2) : 0,
            ];
        }

        return $cashFlow;
    }

    /**
     * Get payment status breakdown
     */
    public function getPaymentStatusBreakdown()
    {
        if (!$this->has('incomes', 'payment_status')) {
            return [];
        }

        return DB::table('incomes')
            ->select(
                'payment_status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount_received) as total_amount')
            )
            ->groupBy('payment_status')
            ->get()
            ->map(fn($item) => [
                'status' => $item->payment_status,
                'count' => $item->count,
                'total' => (float) $item->total_amount,
            ])
            ->toArray();
    }

    /**
     * Get outstanding receivables (unpaid invoices)
     */
    public function getOutstandingReceivables()
    {
        if (!$this->has('incomes')) {
            return [
                'total_outstanding' => 0,
                'count' => 0,
                'pending_count' => 0,
                'overdue_count' => 0,
            ];
        }

        $outstanding = Income::whereIn('payment_status', ['Pending', 'Overdue', 'partially paid'])
            ->get();

        return [
            'total_outstanding' => (float) $outstanding->sum('amount_remaining'),
            'count' => $outstanding->count(),
            'pending_count' => $outstanding->where('payment_status', 'Pending')->count(),
            'overdue_count' => $outstanding->where('payment_status', 'Overdue')->count(),
            'partially_paid_count' => $outstanding->where('payment_status', 'partially paid')->count(),
        ];
    }

    /**
     * Get expense breakdown by payment method
     */
    public function getExpenseByMethod($startDate = null, $endDate = null)
    {
        if (!$this->has('expenses')) {
            return [];
        }

        $startDate = $startDate ?? Carbon::today()->startOfMonth();
        $endDate = $endDate ?? Carbon::today()->endOfDay();

        return DB::table('expenses')
            ->select(
                'method',
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('method')
            ->orderByDesc('total')
            ->get()
            ->map(fn($item) => [
                'method' => $item->method ?? 'Unknown',
                'total' => (float) $item->total,
                'count' => $item->count,
            ])
            ->toArray();
    }

    /**
     * Get transaction totals by category (optionally filtered by type)
     * Structure: [ { category: string, total: float, count: int } ]
     */
    public function getTransactionsByCategory($startDate = null, $endDate = null, $type = null)
    {
        if (!$this->has('transactions')) {
            return [];
        }

        $startDate = $startDate ?? Carbon::today()->startOfMonth();
        $endDate = $endDate ?? Carbon::today()->endOfDay();

        $query = DB::table('transactions')
            ->select(
                DB::raw('COALESCE(category, "Uncategorized") as category'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('date', [$startDate, $endDate]);

        if ($type) {
            $query->where('type', $type);
        }

        return $query
            ->groupBy('category')
            ->orderByDesc(DB::raw('SUM(amount)'))
            ->get()
            ->map(fn($item) => [
                'category' => $item->category ?? 'Uncategorized',
                'total' => (float) $item->total,
                'count' => (int) $item->count,
            ])
            ->toArray();
    }

    /**
     * Get quick stats for dashboard cards
     */
    public function getQuickStats()
    {
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfToday = $today->endOfDay();

        return [
            'today_income' => $this->has('incomes', 'amount_received')
                ? (float) Income::whereDate('received_at', $today)->sum('amount_received')
                : 0,
            'today_expense' => $this->has('expenses', 'amount')
                ? (float) Expense::whereDate('date', $today)->sum('amount')
                : 0,
            'month_income' => $this->has('incomes', 'amount_received')
                ? (float) Income::whereBetween('received_at', [$startOfMonth, $endOfToday])->sum('amount_received')
                : 0,
            'month_expense' => $this->has('expenses', 'amount')
                ? (float) Expense::whereBetween('date', [$startOfMonth, $endOfToday])->sum('amount')
                : 0,
            'outstanding' => $this->getOutstandingReceivables()['total_outstanding'],
            'total_transactions' => $this->has('transactions')
                ? (int) Transaction::count()
                : 0,
        ];
    }
}

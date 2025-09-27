<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Transaction; // change name if you use a different model name

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // change/add role middleware if needed
    }

    public function index(Request $request)
    {
        // Date helpers
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        $startOfYear = $today->copy()->startOfYear();

        // Basic totals / counts
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'active')->count(); // optional: depends on your schema

        // Payments & sums
        $paymentsTotal = (float) Payment::sum('amount');
        $paymentsThisMonth = (float) Payment::whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount');
        $paymentsThisYear = (float) Payment::whereBetween('created_at', [$startOfYear, $today->endOfDay()])->sum('amount');

        // Expenses & sums
        $expensesTotal = (float) Expense::sum('amount');
        $expensesThisMonth = (float) Expense::whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->sum('amount');

        // Transactions (count + sums if have amount)
        $transactionsCount = Transaction::count();
        $transactionsThisMonth = Transaction::whereBetween('created_at', [$startOfMonth, $today->endOfDay()])->count();
        // If Transaction has 'amount' column:
        $transactionsTotal = schema_has_column('transactions', 'amount')
            ? (float) Transaction::sum('amount')
            : null;

        // Monthly series for the last 6 months (for simple charts)
        $months = [];
        $paymentsMonthly = [];
        $expensesMonthly = [];
        for ($i = 5; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $label = $dt->format('M Y');
            $months[] = $label;

            $mStart = $dt->copy()->startOfMonth();
            $mEnd = $dt->copy()->endOfMonth();

            $paymentsMonthly[] = (float) Payment::whereBetween('created_at', [$mStart, $mEnd])->sum('amount');
            $expensesMonthly[] = (float) Expense::whereBetween('created_at', [$mStart, $mEnd])->sum('amount');
        }

        // Recent lists
        $recentEmployees = Employee::latest()->limit(5)->get();
        $recentPayments  = Payment::latest()->limit(7)->get();
        $recentExpenses  = Expense::latest()->limit(7)->get();
        $recentTransactions = Transaction::latest()->limit(7)->get();

        return view('dashboard', compact(
            'totalEmployees',
            'activeEmployees',
            'paymentsTotal',
            'paymentsThisMonth',
            'paymentsThisYear',
            'expensesTotal',
            'expensesThisMonth',
            'transactionsCount',
            'transactionsThisMonth',
            'transactionsTotal',
            'months',
            'paymentsMonthly',
            'expensesMonthly',
            'recentEmployees',
            'recentPayments',
            'recentExpenses',
            'recentTransactions'
        ));
    }
}

/**
 * helper: cheap runtime check for column existence to avoid migration errors.
 * (If you prefer, delete this helper and assume columns exist.)
 */
if (! function_exists('schema_has_column')) {
    function schema_has_column(string $table, string $column): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
